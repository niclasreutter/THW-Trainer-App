/**
 * THW-Trainer Offline Database Manager
 * Speichert alle Fragen in IndexedDB für Offline-Nutzung
 */

class OfflineDB {
    constructor() {
        this.dbName = 'thw-trainer-db';
        this.version = 2;
        this.db = null;
    }

    /**
     * Initialisiere die Datenbank
     */
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.version);
            
            request.onerror = () => {
                console.error('❌ IndexedDB error:', request.error);
                reject(request.error);
            };
            
            request.onsuccess = () => {
                this.db = request.result;
                console.log('✅ OfflineDB initialized');
                resolve(this.db);
            };
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Questions Store
                if (!db.objectStoreNames.contains('questions')) {
                    const questionStore = db.createObjectStore('questions', { keyPath: 'id' });
                    questionStore.createIndex('lernabschnitt', 'lernabschnitt', { unique: false });
                    console.log('✅ Created questions store');
                }
                
                // Cache metadata
                if (!db.objectStoreNames.contains('metadata')) {
                    db.createObjectStore('metadata', { keyPath: 'key' });
                    console.log('✅ Created metadata store');
                }
                
                // Pending answers (für Background Sync)
                if (!db.objectStoreNames.contains('pending-answers')) {
                    db.createObjectStore('pending-answers', { keyPath: 'id', autoIncrement: true });
                    console.log('✅ Created pending-answers store');
                }
                
                // Guest progress (für anonyme Nutzer)
                if (!db.objectStoreNames.contains('guest-progress')) {
                    const guestStore = db.createObjectStore('guest-progress', { keyPath: 'question_id' });
                    guestStore.createIndex('answered_at', 'answered_at', { unique: false });
                    console.log('✅ Created guest-progress store');
                }
                
                // Guest statistics
                if (!db.objectStoreNames.contains('guest-stats')) {
                    db.createObjectStore('guest-stats', { keyPath: 'key' });
                    console.log('✅ Created guest-stats store');
                }
            };
        });
    }

    /**
     * Speichere alle Fragen in IndexedDB
     */
    async saveQuestions(questions) {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        const tx = this.db.transaction(['questions', 'metadata'], 'readwrite');
        const questionStore = tx.objectStore('questions');
        const metaStore = tx.objectStore('metadata');
        
        // Save all questions
        let savedCount = 0;
        for (const question of questions) {
            try {
                await questionStore.put(question);
                savedCount++;
            } catch (error) {
                console.error('Error saving question:', question.id, error);
            }
        }
        
        // Save metadata
        await metaStore.put({
            key: 'last_sync',
            value: new Date().toISOString(),
            count: savedCount,
            version: this.version
        });
        
        console.log(`✅ Saved ${savedCount} questions to IndexedDB`);
        return savedCount;
    }

    /**
     * Hole alle Fragen
     */
    async getAllQuestions() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('questions', 'readonly');
            const store = tx.objectStore('questions');
            const request = store.getAll();
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Hole eine Frage nach ID
     */
    async getQuestionById(id) {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('questions', 'readonly');
            const store = tx.objectStore('questions');
            const request = store.get(parseInt(id));
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Hole Fragen nach Lernabschnitt
     */
    async getQuestionsByLernabschnitt(lernabschnitt) {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('questions', 'readonly');
            const store = tx.objectStore('questions');
            const index = store.index('lernabschnitt');
            const request = index.getAll(parseInt(lernabschnitt));
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Hole Metadata (letzter Sync, etc.)
     */
    async getMetadata() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('metadata', 'readonly');
            const store = tx.objectStore('metadata');
            const request = store.get('last_sync');
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Anzahl der gespeicherten Fragen
     */
    async getQuestionCount() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('questions', 'readonly');
            const store = tx.objectStore('questions');
            const request = store.count();
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Lösche alle Fragen (für komplettes Neu-Sync)
     */
    async clearQuestions() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('questions', 'readwrite');
            const store = tx.objectStore('questions');
            const request = store.clear();
            
            request.onsuccess = () => {
                console.log('✅ Cleared all questions from IndexedDB');
                resolve();
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * ===== OFFLINE ANTWORTEN & FORTSCHRITT =====
     */

    /**
     * Speichere eine Antwort offline (wird später synchronisiert)
     */
    async savePendingAnswer(answerData) {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        const data = {
            ...answerData,
            timestamp: new Date().toISOString(),
            synced: false
        };

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('pending-answers', 'readwrite');
            const store = tx.objectStore('pending-answers');
            const request = store.add(data);
            
            request.onsuccess = () => {
                console.log('✅ Answer saved offline:', answerData.question_id);
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Hole alle ungesynchronisierten Antworten
     */
    async getPendingAnswers() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('pending-answers', 'readonly');
            const store = tx.objectStore('pending-answers');
            const request = store.getAll();
            
            request.onsuccess = () => {
                const pending = request.result.filter(a => !a.synced);
                resolve(pending);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Markiere Antwort als synchronisiert
     */
    async markAnswerSynced(id) {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('pending-answers', 'readwrite');
            const store = tx.objectStore('pending-answers');
            const getRequest = store.get(id);
            
            getRequest.onsuccess = () => {
                const data = getRequest.result;
                if (data) {
                    data.synced = true;
                    data.syncedAt = new Date().toISOString();
                    const putRequest = store.put(data);
                    putRequest.onsuccess = () => resolve();
                    putRequest.onerror = () => reject(putRequest.error);
                } else {
                    resolve();
                }
            };
            getRequest.onerror = () => reject(getRequest.error);
        });
    }

    /**
     * Lösche alte synchronisierte Antworten (älter als 7 Tage)
     */
    async cleanupSyncedAnswers() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        const sevenDaysAgo = new Date();
        sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('pending-answers', 'readwrite');
            const store = tx.objectStore('pending-answers');
            const request = store.openCursor();
            
            let deleted = 0;
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    const data = cursor.value;
                    if (data.synced && new Date(data.syncedAt) < sevenDaysAgo) {
                        cursor.delete();
                        deleted++;
                    }
                    cursor.continue();
                } else {
                    console.log(`✅ Cleaned up ${deleted} old synced answers`);
                    resolve(deleted);
                }
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Anzahl der pending Antworten
     */
    async getPendingAnswersCount() {
        const pending = await this.getPendingAnswers();
        return pending.length;
    }

    /**
     * ===== GUEST (ANONYMER) FORTSCHRITT =====
     */

    /**
     * Speichere Guest-Antwort (offline)
     */
    async saveGuestAnswer(questionId, userAnswer, isCorrect) {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        const data = {
            question_id: parseInt(questionId),
            user_answer: Array.isArray(userAnswer) ? userAnswer : [userAnswer],
            is_correct: isCorrect,
            answered_at: new Date().toISOString(),
            synced: false
        };

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('guest-progress', 'readwrite');
            const store = tx.objectStore('guest-progress');
            const request = store.put(data);
            
            request.onsuccess = () => {
                console.log('✅ Guest answer saved:', questionId);
                this.updateGuestStats();
                resolve(request.result);
            };
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Hole Guest-Fortschritt
     */
    async getGuestProgress() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('guest-progress', 'readonly');
            const store = tx.objectStore('guest-progress');
            const request = store.getAll();
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Hole Guest-Statistiken
     */
    async getGuestStats() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        const progress = await this.getGuestProgress();
        
        const stats = {
            total_answered: progress.length,
            correct_answers: progress.filter(p => p.is_correct).length,
            wrong_answers: progress.filter(p => !p.is_correct).length,
            accuracy: 0
        };

        if (stats.total_answered > 0) {
            stats.accuracy = Math.round((stats.correct_answers / stats.total_answered) * 100);
        }

        return stats;
    }

    /**
     * Update Guest-Statistiken in DB
     */
    async updateGuestStats() {
        if (!this.db) return;

        const stats = await this.getGuestStats();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('guest-stats', 'readwrite');
            const store = tx.objectStore('guest-stats');
            const request = store.put({
                key: 'current',
                ...stats,
                last_updated: new Date().toISOString()
            });
            
            request.onsuccess = () => resolve();
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Prüfe ob Frage bereits beantwortet (Guest)
     */
    async isQuestionAnsweredByGuest(questionId) {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('guest-progress', 'readonly');
            const store = tx.objectStore('guest-progress');
            const request = store.get(parseInt(questionId));
            
            request.onsuccess = () => resolve(!!request.result);
            request.onerror = () => reject(request.error);
        });
    }

    /**
     * Lösche Guest-Daten (z.B. bei Login)
     */
    async clearGuestData() {
        if (!this.db) {
            throw new Error('Database not initialized');
        }

        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['guest-progress', 'guest-stats'], 'readwrite');
            const progressStore = tx.objectStore('guest-progress');
            const statsStore = tx.objectStore('guest-stats');
            
            Promise.all([
                progressStore.clear(),
                statsStore.clear()
            ]).then(() => {
                console.log('✅ Guest data cleared');
                resolve();
            }).catch(reject);
        });
    }

    /**
     * Exportiere Guest-Daten (für Migration bei Registrierung)
     */
    async exportGuestData() {
        const progress = await this.getGuestProgress();
        const stats = await this.getGuestStats();
        
        return {
            progress,
            stats,
            exported_at: new Date().toISOString()
        };
    }
}

// Global instance
window.offlineDB = new OfflineDB();

// Auto-init when loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.offlineDB.init().catch(console.error);
    });
} else {
    window.offlineDB.init().catch(console.error);
}


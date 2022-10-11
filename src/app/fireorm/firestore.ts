import { Config } from '@foal/core';
import { FirebaseApp, FirebaseOptions, initializeApp } from 'firebase/app';
import { Firestore, getFirestore } from 'firebase/firestore';
import { CollectionManager } from './collections';

const firebaseConfig: FirebaseOptions = {
  apiKey: Config.getOrThrow('fireorm.apiKey'),
  authDomain: Config.getOrThrow('fireorm.authDomain'),
  databaseURL: Config.getOrThrow('fireorm.databaseURL'),
  projectId: Config.getOrThrow('fireorm.projectId'),
  storageBucket: Config.getOrThrow('fireorm.storageBucket'),
  messagingSenderId: Config.getOrThrow('fireorm.messagingSenderId'),
  appId: Config.getOrThrow('fireorm.appId'),
};

// use FirestoreManager as singleton
let firestoreManager: FirestoreManager;

/**
 * Main Firestore Database manager class
 */
export class FirestoreManager {
  private _app: FirebaseApp;
  private _firestore: Firestore;
  private _collectionManager: CollectionManager;

  /**
   *
   * @returns Collection Manager instance
   */
  getCollectionManager(): CollectionManager {
    return this._collectionManager;
  }

  /**
   *
   * @returns FirebaseApp instance
   */
  getApp() {
    if (!this._app) {
      this.initialize();
    }
    return this._app;
  }

  /**
   *
   * @returns Firestore instance
   */
  getFirestore() {
    if (!this._firestore) {
      this.initialize();
    }
    return this._firestore;
  }

  /**
   * instnciate once collection manager
   */
  private initCollectionManager() {
    if (!this._collectionManager) {
      this._collectionManager = new CollectionManager(this);
    }
  }

  /**
   * Initialize firebase app, firestore instance and collection manager within FirestoreManager class
   */
  initialize() {
    this._app = initializeApp(firebaseConfig);
    this._firestore = getFirestore(this._app);
    this.initCollectionManager();
  }
}

/**
 * Function to instantiate and get FirestoreManager as a singleton pattern
 */
export const initFirestore = () => {
  if (!firestoreManager) {
    firestoreManager = new FirestoreManager();
    firestoreManager.initialize();
  }
};

/**
 *
 * @returns Helper to retrieve firestoreManager instance everywhere
 */
export const getFirestoreManager = (): FirestoreManager => {
  if (!firestoreManager) {
    initFirestore();
  }
  return firestoreManager;
};

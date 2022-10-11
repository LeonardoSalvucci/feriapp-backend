import { spy, type SinonSpy } from 'sinon';
import { FirebaseApp } from 'firebase/app';
import { strictEqual } from 'assert';
import { Firestore } from 'firebase/firestore';
import { FirestoreManager } from '../firestore';
import { CollectionManager } from '../collections';
const proxyquire = require('proxyquire').noCallThru().noPreserveCache();

const initializeAppReturns: FirebaseApp = {
  name: 'test',
  options: {},
  automaticDataCollectionEnabled: false,
};

const firestoreReturns: Firestore = {
  type: 'firestore-lite',
  app: initializeAppReturns,
  toJSON: function (): object {
    throw new Error('Function not implemented.');
  },
};

describe('FirestoreManager', () => {
  // This is for "import" whole firebase.ts file with proxyquire
  let firestoreManager: any;

  // Spies variables
  let initializeAppSpy: SinonSpy<[], FirebaseApp>;
  let getFirestoreSpy: SinonSpy<[], Firestore>;

  // this represents the instance of firebase app after calling initializeApp
  const firebase = {
    initializeApp: () => initializeAppReturns,
  };

  // this represents the instance of firestore after calling getFirestore
  const firestore = {
    getFirestore: () => firestoreReturns,
  };

  beforeEach(() => {
    // We are using proxyquire to replace firebase/app and firebase/firestore libraries
    firestoreManager = proxyquire('../firestore', {
      'firebase/app': firebase,
      'firebase/firestore': firestore,
    });

    // Set Spies
    initializeAppSpy = spy(firebase, 'initializeApp');
    getFirestoreSpy = spy(firestore, 'getFirestore');
  });

  afterEach(() => {
    // Restore spies
    initializeAppSpy.restore();
    getFirestoreSpy.restore();
  });

  describe('Firebase functions', () => {
    it('verify if initialization calls firebase functions', () => {
      // Act - initialize FirestoreManager
      firestoreManager.initFirestore();

      // Verify mocks calls
      strictEqual(initializeAppSpy.calledOnce, true);
      strictEqual(getFirestoreSpy.calledOnce, true);
    });

    it('verify calling multiple times initFirestore makes only one call to firebase functions', () => {
      // Act - initialize FirestoreManager twice
      firestoreManager.initFirestore();
      firestoreManager.initFirestore();

      // Verify mocks calls
      strictEqual(initializeAppSpy.calledOnce, true);
      strictEqual(getFirestoreSpy.calledOnce, true);
    });

    it('call getFirestoreManager for first time calls firebase funcions', () => {
      // Act - call firestore getter
      const fm = firestoreManager.getFirestoreManager();

      // Verify mocks calls
      strictEqual(initializeAppSpy.calledOnce, true);
      strictEqual(getFirestoreSpy.calledOnce, true);
    });

    it('getFirestoreManager retrieve a FirestoreManager instance', () => {
      // Act - call firestore global getter
      const fm = firestoreManager.getFirestoreManager();

      // Verify
      strictEqual(fm !== undefined, true);
    });
  });

  describe('FirestoreManager', () => {
    let firestoreManagerInstance: FirestoreManager;

    beforeEach(() => {
      firestoreManagerInstance = firestoreManager.getFirestoreManager();
    });

    it('getApp returns FirebaseApp', () => {
      // Act
      const app = firestoreManagerInstance.getApp();

      // Verify
      strictEqual(app, initializeAppReturns);
    });

    it('getFirestore returns Firestore reference', () => {
      // Act
      const firestoreInstance = firestoreManagerInstance.getFirestore();

      // Verify
      strictEqual(firestoreInstance, firestoreReturns);
    });

    it('getCollectionManager returns CollectionManager instance', () => {
      // Act
      const colManager = firestoreManagerInstance.getCollectionManager();

      // Verify
      strictEqual(colManager instanceof CollectionManager, true);
    });
  });
});

import { spy, type SinonSpy } from 'sinon';

import { strictEqual } from 'assert';
import { FirebaseApp } from 'firebase/app';
import { Firestore } from 'firebase/firestore';
import { FirestoreManager } from '../firestore';
import { CollectionManager } from '../collections';
import {
  initializeAppReturns,
  firestoreReturns,
  mockInitializeFirebase,
  firebaseModule,
  firestoreModule,
} from './helpers';

describe('FirestoreManager', () => {
  // This is for "import" whole firebase.ts file with proxyquire
  let firestoreManager: any;

  // Spies variables
  let initializeAppSpy: SinonSpy<[], FirebaseApp>;
  let getFirestoreSpy: SinonSpy<[], Firestore>;

  beforeEach(() => {
    // Set Spies
    initializeAppSpy = spy(firebaseModule, 'initializeApp');
    getFirestoreSpy = spy(firestoreModule, 'getFirestore');

    // We have to declare proxyquire to have a fresh module every test
    firestoreManager = mockInitializeFirebase('../firestore');
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

    describe('FirestoreManager instances', () => {
      it('getFirestoreManager retrieve a FirestoreManager instance', () => {
        // Act - call firestore global getter
        const fm = firestoreManager.getFirestoreManager();

        // Verify
        strictEqual(fm !== undefined, true);
      });
    });

    it('getFirestoreManager called twice retrieve same instance', () => {
      // Act - call getFirestoreManager twice
      const fm1 = firestoreManager.getFirestoreManager();
      const fm2 = firestoreManager.getFirestoreManager();

      // Verify
      strictEqual(fm1, fm2);
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

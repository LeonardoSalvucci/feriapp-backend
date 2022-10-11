import { FirebaseApp } from 'firebase/app';
import { Firestore } from 'firebase/firestore';

const proxyquire = require('proxyquire').noCallThru().noPreserveCache();

export const initializeAppReturns: FirebaseApp = {
  name: 'test',
  options: {},
  automaticDataCollectionEnabled: false,
};

export const firestoreReturns: Firestore = {
  type: 'firestore-lite',
  app: initializeAppReturns,
  toJSON: function (): object {
    throw new Error('Function not implemented.');
  },
};

// this represents the instance of firebase app after calling initializeApp
export const firebaseModule = {
  initializeApp: () => initializeAppReturns,
};

// this represents the instance of firestore after calling getFirestore
export const firestoreModule = {
  getFirestore: () => firestoreReturns,
};

export function mockInitializeFirebase(module: string) {
  return proxyquire(module, {
    'firebase/app': firebaseModule,
    'firebase/firestore': firestoreModule,
  });
}

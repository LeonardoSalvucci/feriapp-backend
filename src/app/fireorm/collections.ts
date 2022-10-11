import {
  CollectionReference,
  collection,
  DocumentReference,
  setDoc,
  doc,
  QueryDocumentSnapshot,
  SnapshotOptions,
  getDoc,
  getDocs,
} from 'firebase/firestore';
import { type FirestoreManager } from './firestore';
import { BaseDocument } from './documents';
import { getConstructorPluralName } from './utils';
import { FirestoreDataConverter } from '@firebase/firestore';

/**
 * Class for manage collections and documents actions
 */
export class CollectionManager {
  private _firestoreManager: FirestoreManager;
  private collections: Map<string, CollectionReference>;
  private constructors: Map<string, typeof BaseDocument>;

  constructor(firestoreManager: FirestoreManager) {
    this._firestoreManager = firestoreManager;
    this.collections = new Map();
    this.constructors = new Map();
  }

  /**
   *
   * @param collectionName Name of collection to manage
   */
  setCollection<T extends BaseDocument>(
    collectionName: string,
    constructor: typeof BaseDocument
  ): void {
    if (!this.collections.has(collectionName)) {
      const colRef: CollectionReference<T> = collection(
        this._firestoreManager.getFirestore(),
        collectionName
      ).withConverter<T>(this.withConverter());
      this.collections.set(collectionName, colRef);
      this.constructors.set(colRef.path, constructor);
    }
  }

  /**
   *
   * @param collectionNameOrDocument {string } Name or Document to get it's collection reference
   */
  getCollectionRef<T extends BaseDocument>(
    collectionNameOrDocument: string | T
  ): CollectionReference<T> {
    const colName =
      typeof collectionNameOrDocument === 'string'
        ? collectionNameOrDocument
        : getConstructorPluralName(collectionNameOrDocument.constructor);
    const colRef = this.collections.get(colName);
    if (!colRef) {
      throw new Error(`Collection ${collectionNameOrDocument} is not defined`);
    }
    return colRef as CollectionReference<T>;
  }

  /**
   *
   * @param document Document Entity
   * @returns Document reference with generic converters
   */
  getDocRef<T extends BaseDocument>(document: T) {
    const docRef: DocumentReference<T> = doc(
      this.getCollectionRef(document),
      document.uid
    );
    return docRef;
  }

  /**
   *
   * @param document Document to be saved
   * @dev Save a document to it's collection
   */
  async save<T extends BaseDocument>(document: T) {
    return await setDoc(this.getDocRef(document), document);
  }

  /**
   *
   * @param documentEntity Document Entity Class to query
   * @param uid unique id of document to retrieve
   * @returns Get document by uid or undefined
   */
  async getById<T extends BaseDocument>(
    documentEntity: typeof BaseDocument,
    uid: string
  ): Promise<T | undefined> {
    const colName = getConstructorPluralName(documentEntity);
    const snap = await getDoc<T>(
      doc<T>(this.getCollectionRef<T>(colName), uid)
    );
    if (snap.exists()) {
      return snap.data();
    }
    return;
  }

  /**
   *
   * @param documentEntity Document Entity Class to query
   * @returns all documents of Entity
   */
  async getAll<T extends BaseDocument>(
    documentEntity: typeof BaseDocument
  ): Promise<T[]> {
    const colName = getConstructorPluralName(documentEntity);
    const documents: T[] = [];
    // eslint-disable-next-line @typescript-eslint/ban-ts-ignore
    // @ts-ignore
    const snapshot = await getDocs<T>(this.getCollectionRef(colName));
    snapshot.forEach(doc => {
      documents.push(doc.data());
    });
    return documents;
  }

  /**
   *
   * @returns Generic converters to be assigned to firestore references
   */
  withConverter<T extends BaseDocument>(): FirestoreDataConverter<T> {
    return {
      toFirestore: (document: T) => {
        return document.toObject();
      },
      fromFirestore: (
        snapshot: QueryDocumentSnapshot<T>,
        options: SnapshotOptions
      ) => {
        const data = snapshot.data(options);
        const Entity = this.constructors.get(snapshot.ref.parent.path);
        if (Entity) {
          return Entity.create<T>(data);
        }
        throw new Error('Collection not set');
      },
    };
  }
}

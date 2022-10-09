import 'reflect-metadata';

import { FIELDLIST_METADATA_KEY } from './constants';
import { getFirestoreManager } from './firestore';
import { IBaseDocument } from './types';
import { DocumentData } from 'firebase/firestore';

export class BaseDocument implements IBaseDocument {
  uid: string;

  /**
   * Save document into firestore collection
   */
  async save() {
    return await BaseDocument.getCollectionManager().save(this);
  }

  /**
   * Update firestore document by ref
   */
  update() {}

  /**
   * Remove document from firestore
   */
  delete() {}

  /**
   * Find a document by id
   */
  static async getById<T extends BaseDocument>(
    uid: string
  ): Promise<T | undefined> {
    return await BaseDocument.getCollectionManager().getById<T>(this, uid);
  }

  /**
   *
   * @returns Get all documents
   */
  static async all<T extends BaseDocument>(): Promise<T[]> {
    return await BaseDocument.getCollectionManager().getAll(this);
  }

  /**
   * Create a new Document instance
   */
  static create<T extends BaseDocument>(this, args?: any): T {
    const obj = new this();
    const fieldsList = Reflect.getMetadata(FIELDLIST_METADATA_KEY, obj);
    if (fieldsList instanceof Array && args) {
      fieldsList.map(key => {
        if (args[key]) {
          obj[key] = args[key];
        }
      });
    }
    return obj;
  }

  /**
   *
   * @returns Plain object of this instance
   */
  toObject(): DocumentData {
    const fieldsList = Reflect.getMetadata(FIELDLIST_METADATA_KEY, this);
    const obj = {};
    fieldsList.map((key: string) => {
      if (this[key]) {
        obj[key] = this[key];
      }
    });
    return obj;
  }

  private static getCollectionManager() {
    const fm = getFirestoreManager();
    return fm.getCollectionManager();
  }
}

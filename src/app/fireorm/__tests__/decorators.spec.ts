import 'reflect-metadata';
import { spy, stub } from 'sinon';
import { strictEqual } from 'assert';
const proxyquire = require('proxyquire').noCallThru().noPreserveCache();

import { Field } from '../decorators';
import { FIELDLIST_METADATA_KEY } from '../constants';
import { BaseDocument } from '../documents';

class FieldTest {
  @Field()
  test: string;
}

describe('Decorators', () => {
  describe('Field', () => {
    it('Verify metadata', () => {
      // Prepate - instantiate the testing class
      const fieldTestClass = new FieldTest();
      const fieldList: string[] = Reflect.getMetadata(
        FIELDLIST_METADATA_KEY,
        fieldTestClass
      );

      // Verify
      strictEqual(fieldList instanceof Array, true);
      strictEqual(fieldList.length, 1);
      strictEqual(fieldList.includes('test'), true);
    });
  });

  describe('Collection', () => {
    let getFirestoreManager;

    const firestoreModule = {
      getFirestoreManager: stub().returns({
        getCollectionManager: stub().returns({ setCollection: spy() }),
      }),
    };

    beforeEach(() => {
      getFirestoreManager = proxyquire('../decorators', {
        './firestore': firestoreModule,
      });
    });

    it('using collection decorator adds the new collection in collectionManager', () => {
      // Prepare - use proxyquire instance of decorator
      @getFirestoreManager.Collection
      class TestCollection extends BaseDocument {
        @Field()
        uid = '12345';
      }

      // Act - instantiate TestCollection
      const testCollectionClass = new TestCollection();

      // Verify - test if setCollection was called with correct args
      strictEqual(
        firestoreModule
          .getFirestoreManager()
          .getCollectionManager()
          .setCollection.calledOnceWith(
            'testcollections',
            testCollectionClass.constructor
          ),
        true
      );
    });
  });
});

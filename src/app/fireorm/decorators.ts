import { getFirestoreManager } from './firestore';
import { FIELDLIST_METADATA_KEY } from './constants';
import { getConstructorPluralName } from './utils';
import { BaseDocument } from './documents';

/**
 *
 * @param constructor
 * @returns
 * Decorator to save the tollection of a document entity and it's constructor
 */
export function Collection<T extends typeof BaseDocument>(constructor: T) {
  const collectionName = getConstructorPluralName(constructor);

  const fm = getFirestoreManager();
  const cm = fm.getCollectionManager();
  cm.setCollection(collectionName, constructor);
  return constructor;
}

/**
 *
 * Decorator to set a property of a document
 */
export function Field() {
  return (target: Record<string, any>, propertyKey: string | symbol) => {
    // append propertyKey to field list metadata
    Reflect.defineMetadata(
      FIELDLIST_METADATA_KEY,
      [
        ...(Reflect.getMetadata(FIELDLIST_METADATA_KEY, target) ?? []),
        propertyKey,
      ],
      target
    );
  };
}

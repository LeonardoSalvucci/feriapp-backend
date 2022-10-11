import { notStrictEqual, strictEqual } from 'assert';
import { getConstructorPluralName } from '../utils';

describe('Fire ORM - utils', () => {
  describe('getConstructorPluralName', () => {
    // eslint-disable-next-line @typescript-eslint/quotes
    it("singular class name must add an 's' at the end", () => {
      class Test {}
      const pluralName = getConstructorPluralName(Test);
      strictEqual(pluralName.endsWith('s'), true);
    });

    it('pluralName must lower case class name', () => {
      class Test {}
      const pluralName = getConstructorPluralName(Test);
      strictEqual(pluralName, 'tests');
      notStrictEqual(pluralName, 'Test');
      notStrictEqual(pluralName, 'Tests');
    });

    it('pluralName must not add another s if class name is plural', () => {
      class Tests {}
      const pluralName = getConstructorPluralName(Tests);
      strictEqual(pluralName, 'tests');
    });

    it('function must fail if a native type var is passed', () => {
      try {
        const nativeVar = 'Hi';
        getConstructorPluralName(nativeVar);
      } catch (error) {
        if (error instanceof TypeError) {
          strictEqual(
            error.message,
            'This function expects a constructor as argument'
          );
        } else {
          throw error;
        }
      }
    });
  });
});

export const getConstructorPluralName = (constructor: any): string => {
  try {
    let pluralName = constructor.name.toLowerCase();
    pluralName = pluralName.endsWith('s') ? pluralName : `${pluralName}s`;
    return pluralName;
  } catch (error) {
    if (error instanceof TypeError) {
      throw new TypeError('This function expects a constructor as argument');
    }
    throw error;
  }
};

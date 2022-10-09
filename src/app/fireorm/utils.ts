export const getConstructorPluralName = (constructor: any) => {
  let pluralName = constructor.name.toLowerCase();
  pluralName = pluralName.endsWith('s') ? pluralName : `${pluralName}s`;

  return pluralName;
};

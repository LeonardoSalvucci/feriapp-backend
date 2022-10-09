import { Field, BaseDocument, Collection } from '../fireorm';

@Collection
export class Person extends BaseDocument {
  @Field()
  uid: string;

  @Field()
  firstName: string;

  @Field()
  lastName: string;

  @Field()
  phone: number;
}

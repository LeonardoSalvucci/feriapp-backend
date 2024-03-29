import { Field, BaseDocument, Collection } from '../fireorm';

@Collection
export class User extends BaseDocument {
  @Field()
  uid: string;

  @Field()
  firstName: string;

  @Field()
  lastName: string;

  @Field()
  email: string;
}

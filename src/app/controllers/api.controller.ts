import { Context, Get, HttpResponseOK } from '@foal/core';
import { User, Person } from '../entities';

export class ApiController {
  @Get('/')
  async index(ctx: Context) {
    const user = User.create({
      uid: '12346',
      firstName: 'Luciana',
      lastName: 'Lujan',
      email: 'lucianaelujan@hotmail.com',
    });
    await user.save();
    return new HttpResponseOK(user);
  }

  @Get('/all')
  async all(ctx: Context) {
    return new HttpResponseOK(await User.all());
  }

  @Get('/all-persons')
  async allPersons(ctx: Context) {
    return new HttpResponseOK(await Person.all());
  }

  @Get('/person')
  async person(ctx: Context) {
    const person = Person.create({
      uid: '12345-person',
      firstName: 'Ariel',
      lastName: 'Hipolito',
      phone: 3415702865,
    });
    await person.save();
    return new HttpResponseOK(person);
  }

  @Get('/get-by-id')
  async readUser(ctx: Context) {
    const response = await User.getById('12345');

    return new HttpResponseOK(response);
  }

  @Get('/person-by-id')
  async readPerson(ctx: Context) {
    const response = await Person.getById('12345-person');

    return new HttpResponseOK(response);
  }
}

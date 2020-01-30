<?php

namespace App\Http\Controllers\Api\Contacts;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Psy\Exception\TypeErrorException;

class ContactsController extends Controller
{
    public function create(Request $request) {
        $this->validate($request,[
            'first_name'=>'required',
            'last_name'=>'required',
            'phone'=>'required',
            'mail'=>'required'
        ]);
        $contact = Contact::create($request->all());
        return handleResponseWithData(true, $contact);
    }

    public function share(Request $request, $contact_id) {
        $request['contact_id'] = $contact_id;
        $this->validate($request, [
            'contact_id' => 'exists:contacts,id',
            'share_to' => 'required'
        ]);
        $contact = Contact::find($request->contact_id);
        try {
            $contact->share($request->share_to);
            return handleResponse(true, 'Contact was shared with groups');
        } catch (TypeErrorException $e) {
            return handleResponse(false, $e->getMessage());
        } 
    }

    public function remove(Request $request, $contact_id) { // This function remove contact from all groups and delete from database
        $request['contact_id'] = $contact_id;
        $this->validate($request, [
            'contact_id' => 'exists:contacts,id'
        ]);

        Contact::find($request->contact_id)->delete();

        return handleResponse(true, 'The contact was deleted from all groups');
    }
}

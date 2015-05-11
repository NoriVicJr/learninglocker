<?php

use Locker\Repository\User\UserRepository as UserRepo;
use Locker\Repository\Lrs\Repository as LrsRepo;
use Locker\Repository\Client\Repository as ClientRepo; 

class ClientController extends BaseController {

  protected $user, $lrs, $client;
  
  /**
   * Constructs a new ClientController.
   * @param UserRepo $user
   * @param LrsRepo $lrs
   * @param ClientRepo $client
   */
  public function __construct(UserRepo $user, LrsRepo $lrs, ClientRepo $client) {
    $this->user = $user;
    $this->lrs = $lrs;
	  $this->client = $client;
  }
  
  /**
   * Load the manage clients page.
   * @param String $lrs_id
   * @return View
   */
  public function manage($lrs_id) {
    $opts = ['user' => \Auth::user()];
    $lrs = $this->lrs->show($lrs_id, $opts);
    $lrs_list = $this->lrs->index($opts);
	  $clients = $this->client->index([
      'lrs_id' => $lrs->_id
    ]);
    return View::make('partials.client.manage', [
      'clients' => $clients,
      'lrs' => $lrs,
      'list' => $lrs_list
		]);
  }
  
   /**
   * Load the manage clients page.
   * @param String $lrs_id
   * @param String $id
   * @return View
   */
  public function edit($lrs_id, $id) {
    $opts = ['user' => \Auth::user()];
    $lrs = $this->lrs->show($lrs_id, $opts);
    $lrs_list = $this->lrs->index($opts);
    $client = $this->client->show([
      'lrs_id' => $lrs->_id
    ]);
	 	return View::make('partials.client.edit', [
      'client' => $client,
      'lrs' => $lrs,
      'list' => $lrs_list
		]);
  }
  
  /**
   * Create a new client.
   * @param String $lrs_id
   * @return View
   */
  public function create($lrs_id) {
    $opts = ['user' => \Auth::user()];
    $lrs = $this->lrs->show($lrs_id, $opts);
	  $opts = ['lrs_id' => $lrs->id];
	
    if ($this->client->store([], $opts)) {
      $message_type = 'success';
      $message = trans('lrs.client.created_sucecss');
    } else {
      $message_type = 'error';
      $message = trans('lrs.client.created_fail');
    }
    
    return Redirect::back()->with($message_type, $message);
  }

  /**
   * Update the specified resource in storage.
   * @param String $lrs_id Id of the LRS.
   * @param String $id Id of the client.
   * @return View
   */
  public function update($lrs_id, $id){
    if ($this->client->update($id, Input::all(), [
      'lrs_id' => $lrs_id
    ])) {
      $redirect_url = '/lrs/'.$lrs_id.'/client/manage#'.$id;
      return Redirect::to($redirect_url)->with('success', trans('lrs.client.updated'));
    }

    return Redirect::back()
      ->withInput()
      ->withErrors($this->client->errors());
  }

  /**
   * Remove the specified resource from storage.
   * @param String $lrs_id
   * @param String $id
   * @return View
   */
  public function destroy($lrs_id, $id){
	  if ($this->client->destroy($id, [
      'lrs_id' => $lrs_id
    ])) {
      $message_type = 'success';
      $message = trans('lrs.client.delete_client_success');
    } else {
      $message_type = 'error';
      $message = trans('lrs.client.delete_client_error');
    }
	
    return Redirect::back()->with($message_type, $message);
  }
}

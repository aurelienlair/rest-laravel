<?php
namespace App\Repositories;

interface Criteria 
{
	/**
     * Return a where data structure which will be syntatically supported
     * by the storage system used by the repository
     *
	 * @return mixed 
	 */
	public function where();
}

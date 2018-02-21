<?php
namespace App\Repositories;

use App\Entities\Actor;

interface ActorRepository
{
	/**
     * Return all the Actors of the repository 
     *
	 * @return iterable|null
	 */
	public function findAll(): ?iterable;

	/**
     * Find one or more Actors within the repository according to a Criteria
     *
	 * @return iterable|null
	 */
	public function findBy(Criteria $criteria): ?iterable;

	/**
	 * Add an Actor to the repository
	 */
	public function add(Actor $actor);

	/**
	 * Update an existing Actor to the repository
	 */
	public function update(Actor $actor);

	/**
	 * Remove an Actor from the repository
	 */
	public function remove(Actor $actor);
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Utilisateur
 * 
 * @property int $id
 * @property string|null $login
 * @property string|null $password
 * 
 * @property Collection|Compte[] $comptes
 * @property Collection|Famille[] $familles
 * @property Collection|Partage[] $partages
 *
 * @package App\models
 */
class Utilisateur extends Model
{
	protected $table = 'utilisateur';
	public $timestamps = false;

	protected $fillable = [
		'login',
		'password'
	];

	public function comptes()
	{
		return $this->hasMany(Compte::class, 'idUtilisateur');
	}

	public function familles()
	{
		return $this->hasMany(Famille::class, 'idUtilisateur');
	}

	public function partages()
	{
		return $this->hasMany(Partage::class, 'idUtilisateur');
	}
}

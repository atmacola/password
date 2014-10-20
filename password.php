<?php
/**
 * permets d'encoder une chaîne de caractères en vue d'insertion comme mot de passe ou comparaison
 * 
 * password::encode('MonPassword') Renvoi un array
 * Celui-ci contient $array['password'] et $array['salt'] prêt à insérer dans la base de données
 * Le mot de passe n'est pas déchiffrable, deux mots de passe identiques donneront des valeurs
 * totalement différentes.
 * 
 * password::decode('MonPassword','fbe1b8b03395d1810b4c46dbe8c09f9def3eb662','47d64b65dbfabed955228b4f71f78971')
 * tout comme 
 * password::decode('MonPassword','83a818713dea66a62212c9b813192df676cdea82','107fe5ae481e1c4c59b58824cd4083c9')
 * Reverra true, car le mot de passe correspond ; mais renverra false au moindre changement. 
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @category   Encryption
 * @package    atmacola/password
 * @author     Ludovic Ganée <www.ludo-portfolio.fr>
 * @license    http://www.gnu.org/licenses/
 * @version    1.0
 */
class password{ 
	
	/**
	 * Encode une chaîne de caractères.
	 * Permets une comparaison avec la function decode.
	 * Un même mot de passe renverra des résultats toujours
	 * différents.
	 * 
	 * @param String $str
	 * @return String array['password']
	 * @return String array['salt']
	 */
	public static function encode($str){		
		/**
		 * @var $salt - On tire une base de 32 chars aléatoire
		 * @var $pos - La base du vrai salt dépend de la taille de la chaîne à encoder
		 * @var $finalSalt - Grain de sel pris dans $salt, a différents endroits, en fonction de $pos
		 */ 
		$salt = md5(rand().'azerty!');
		$pos = strlen($str);
		$finalSalt = '';
		
		while (strlen($finalSalt) < 8){
			if ($pos > 32) $pos -= 31;
			$pos += 8;
			$finalSalt .= substr($salt, $pos, 1);
		}
		
		/**
		 * @var $finalPassword - Mix de toutes les valeurs			
		 */
		$finalPassword = sha1($salt.$str.$finalSalt);
		
		return array('password' => $finalPassword, 'salt' => $salt);
	}
	
	/**
	 * Permets de comparer une chaine de caractère avec une autre encodée.
	 * Le premier param, doit être le même que celui qui a servi avec la function encode()
	 * Dans le cas contraire, ça renverra un false
	 * 
	 * On encrypt le $passwordSaisi avec le $saltBase et on compare le résultat avec $passwordBase
	 * 
	 * @param String $passwordSaisi
	 * @param String $passwordBase
	 * @param String $saltBase
	 * @return boolean
	 */
	public static function decode($passwordSaisi, $passwordBase, $saltBase){
		if (!$passwordSaisi || !$passwordBase || !$saltBase) return false;
		
		/**
		 * @var $pos - La base du vrai salt dépend de la taille de la chaine à encoder
		 * @var $finalSalt - Grain de sel pris dans $salt, a différents endroits, en fonction de $pos
		 */
		$pos = strlen($passwordSaisi);
		$finalSalt = '';
		while (strlen($finalSalt) < 8){
			if ($pos > 32) $pos -= 31;
			$pos += 8;
			$finalSalt .= substr($saltBase, $pos, 1);
		}
		
		/**
		 * @var $finalPassword - Mix de toutes les valeurs
		 */
		$finalPassword = sha1($saltBase.$passwordSaisi.$finalSalt);
		
		return $finalPassword == $passwordBase;
	}
}
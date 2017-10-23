<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 23/10/17
 * Time: 15:31
 */

namespace Cydream\PlatformBundle\Antispam;


class CydreamAntispam
{
    private $mailer;
    private $locale;
    private $minLength;

    public function __construct(\Swift_Mailer $mailer, $locale, $minLength)
    {
        $this->mailer    = $mailer;
        $this->locale    = $locale;
        $this->minLength = (int) $minLength;
    }
   /**
     * Vérifie si le texte est un spam ou non
     *
     * @param string $text
     * @return bool
     */
    public function isSpam($text)
    {
        return mb_strlen($text) < $this->minLength;
    }
}
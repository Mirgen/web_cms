<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RedirectionsRoute
 *
 * @author Jiri Kvapil
 */
class RedirectionsRoute extends \Nette\Application\Routers\Route
{
    /* table of redirections: */
    private $redirections = array(
        "reference"                 => 28,
        "materialy-a-technologie"   => 19,
        "nabytek-na-miru"           => 11,
        "o-mne"                     => 19,
        "kontakt"                   => 18,
        "kuchyne-a-kuchynske-linky-na-miru" => 12,
        "obyvaci-pokoje-na-miru"    => 13,
        "loznice-a-postele"         => 15,
        "koupelny-na-miru"          => 14,
        "vybaveni-kancelari"        => 30,
        "nabytek-do-kancelare"      => 30,
        "lekarny-a-ambulance"       => 32,
        "detsky-nabytek"            => 33,
        "ostatni-nabytek-a-dalsi-vyrobky"  => 34,
        "nabytek-do-ostatnich-prostor"  => 34,
    );

    public function match(\Nette\Http\IRequest $httpRequest)
    {
        $request = parent::match($httpRequest);

        if(isset($request->parameters['nice_url_segment']))
        {
            if(array_key_exists ( $request->parameters['nice_url_segment'],$this->redirections )){
                $request->setPresenterName('Front:Redirections');
                $request->setParameters(
                        array(
                                'presenter' => 'Redirections',
                                'action' => 'Default',
                                'id' => $this->redirections[$request->parameters['nice_url_segment']],
                        )
                );
                return $request;
            }
        }

        return  NULL;
    }
}
?>
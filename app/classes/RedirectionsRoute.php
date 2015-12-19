<?php

/**
 *
 * @author Jiri Kvapil
 */
class RedirectionsRoute extends \Nette\Application\Routers\Route
{
    /* table of redirections: */
    private $redirections = array();

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
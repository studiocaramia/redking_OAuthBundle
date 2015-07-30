<?php

namespace Redking\Bundle\OAuthBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RedkingOAuthBundle extends Bundle
{
    public function getParent()  
    {  
        return 'FOSOAuthServerBundle';  
    } 
}

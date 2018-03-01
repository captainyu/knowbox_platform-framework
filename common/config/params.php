<?php
return [
    'requestId'       => (uniqid().str_pad(rand(1,999), 3, 0, STR_PAD_LEFT)),
    'auth_base_model'=>'',
    'dbmodel_user'=>'',
    'dbmodel_user_role'=>'',
    'dbmodel_role_priv'=>''
];

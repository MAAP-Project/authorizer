<?php
/**
 * Monochrome Pro.
 *
 * A MAAP template to allow custom profile management
 *
 * Template Name: MAAP Profile
 *
 * @author  BSatorius
 */
?>

<?php get_header(); ?>

<?php

$proxyTicketEnc = $_SESSION['maap-profile-proxyGrantingTicket'];

$fp=fopen("/var/www/dit.maap-project.org/mp-private.key","r");  // Update during deployment
$private_maap_portal_key=fread($fp,8192);
fclose($fp);

$res = openssl_get_privatekey($private_maap_portal_key);

openssl_private_decrypt(base64_decode($proxyTicketEnc), $proxyTicketDec, $res);

$maap_api = 'api.dit.maap-project.org'; // Update during deployment
$maap_api_profile = 'https://'. $maap_api . '/api/members/self';
$maap_api_sshKey = $maap_api_profile . '/sshKey';

$self_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$ssh_key_name = '';
$ssh_key_dt = '';


//If submitting a new key, update the MAAP profile
//Otherwise, load the existing profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ch = curl_init();

    $key_file = curl_file_create(realpath($_FILES['file_upl']['tmp_name']), $_FILES['file_upl']['type'], $_FILES['file_upl']['name']);

    $data = array('file' => $key_file);             
    curl_setopt($ch, CURLOPT_URL, $maap_api_sshKey);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'proxy-ticket:' . $proxyTicketDec
    ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);

    $json = json_decode($result);

    $ssh_key_name = $json->public_ssh_key_name;
    $ssh_key_dt = $json->public_ssh_key_modified_date;

    curl_close($ch);
} else {
    $ch = curl_init();

    if ( isset($_GET['del']) ) {
	    curl_setopt($ch, CURLOPT_URL, $maap_api_sshKey);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    } else {
    	curl_setopt($ch, CURLOPT_URL, $maap_api_profile);
        curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'proxy-ticket:' . $proxyTicketDec
    ));

    $result = curl_exec($ch);

    $json = json_decode($result);

    $ssh_key_name = $json->public_ssh_key_name;
    $ssh_key_dt = $json->public_ssh_key_modified_date;

    curl_close($ch);
}
?>
 
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <div class="entry-content" itemprop="text">
            <?php if ($_SESSION['maap-profile']) { ?>
                <form name="file_up" id="file_up" action="" method="POST" enctype="multipart/form-data">
                    <h2>Profile</h2>
                    <h3>
                        <?php echo $_SESSION['maap-profile']['given_name']. ' ' .$_SESSION['maap-profile']['family_name'] ?>			
                        <span style="font-weight: 300;font-family: &quot;Merriweather&quot;,serif;font-size:  18px;margin-left: 10px;">
                                <?php echo ' ('.$_SESSION['maap-profile']['email'].')' ?>		
                        </span>
                    </h3>
                    <p>
                    </p>
                    <div class="wp-block-atomic-blocks-ab-accordion ab-block-accordion">
                        <details>
                            <summary class="ab-accordion-title">Account details</summary>
                            <div class="ab-accordion-text">
                                <ul>
                                    <?php
                                    $display_attrs = array(
                                            "clientName",
                                            "preferred_username",
                                            "affiliation",
                                            "email",
                                            "display_name",
                                            "organization",
                                            "study_area",
                                            "status");

                                    foreach ((array)$_SESSION['maap-profile'] as $array_key) {
                                            $key_name = array_search($array_key, $_SESSION['maap-profile']);

                                            if (in_array($key_name, $display_attrs)) {
                                                    echo '<li>';
                                                    echo $key_name.': '.'<b>'.$array_key.'</b>';
                                                    echo '</li>';
                                            }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </details>
                    </div>
		            <p>
                    </p>
                    <div>
                        Public SSH Key 
                    </div>
                    <div style="font-size: 14px;">
                            Your public SSH key allows you to establish a secure connection between your computer and your MAAP workspaces. To add an SSH key, you need to generate one or use and existing key.
                    </div>
		    <?php if ($ssh_key_name != '' ) { ?>
                    <div style="margin-top: 10px; font-size: 16px">
                            <i aria-hidden="true" data-hidden="true" class="fa fa-key settings-list-icon d-none d-sm-block" style="margin-right: 5px;color: #777777;"></i>
                            <?php echo $ssh_key_name . ' - created ' . $ssh_key_dt ?>   
                            <a href="<?php echo $self_link . '?del=1' ?>"><i aria-hidden="true" data-hidden="true" class="fa fa-trash" style="font-size: 14px; margin-left: 10px"></i></a>
                    </div>    
		    <?php } ?>
                    <div style="margin-top: 10px;">
                        <input type="file" name="file_upl" id="file_upl">
                    </div>
                </form>	
            <?php } else { echo '<p><a href="/">Log back in</a> to view your profile.</p>';  } ?>
        </div>

</div>
 
    </main><!-- .site-main -->
 
</div><!-- .content-area -->
 
<script>
document.getElementById("file_upl").onchange = function() {
    document.getElementById("file_up").submit();
}

</script>

<?php get_footer(); ?>
<?php
/**
 * ImageCache2 - �������ۑ�����
 */

// {{{ p2��{�ݒ�ǂݍ���&�F��

require_once __DIR__ . '/../init.php';

$_login->authorize();

// �����G���[
if (empty($_POST['host'])) {
    // �����̎w�肪�ςł�
    echo 'null';
    exit;
}

$el = error_reporting(E_ALL & ~E_NOTICE);
$salt = 'post' . $_POST['host'] . $_POST['bbs'] . $_POST['key'];
error_reporting($el);

if (!isset($_POST['csrfid']) or $_POST['csrfid'] != P2Util::getCsrfId($salt)) {
    // �s���ȃ|�X�g�ł�
    echo 'null';
    exit;
}

// }}}
// {{{ HTTP�w�b�_

P2Util::header_nocache();
header('Content-Type: text/plain; charset=UTF-8');

// }}}
// {{{ ������
$post_param_keys    = array('bbs', 'key', 'time', 'FROM', 'mail', 'MESSAGE', 'subject', 'submit');
$post_internal_keys = array('host', 'sub', 'popup', 'rescount', 'ttitle_en');
foreach ($post_param_keys as $pk) {
    ${$pk} = (isset($_POST[$pk])) ? mb_convert_encoding($_POST[$pk], 'CP932', 'UTF-8, sjis-win') : '';
}
foreach ($post_internal_keys as $pk) {
    ${$pk} = (isset($_POST[$pk])) ? $_POST[$pk] : '';
}

// ������΂�livedoor�ړ]�ɑΉ��Bpost���livedoor�Ƃ���B
$host = P2HostMgr::adjustHostJbbs($host);

// machibbs�AJBBS@������� �Ȃ�
if (P2HostMgr::isHostMachiBbs($host) or P2HostMgr::isHostJbbsShitaraba($host)) {
    /* compact() �� array_combine() ��POST����l�̔z������̂ŁA
       $post_param_keys �� $post_send_keys �̒l�̏����͑�����I */
    //$post_param_keys  = array('bbs', 'key', 'time', 'FROM', 'mail', 'MESSAGE', 'subject', 'submit');
    $post_send_keys     = array('BBS', 'KEY', 'TIME', 'NAME', 'MAIL', 'MESSAGE', 'SUBJECT', 'submit');
// 2ch
} else {
    $post_send_keys = $post_param_keys;
}
$post = array_combine($post_send_keys, compact($post_param_keys));
unset($post['submit']);

// }}}
// {{{ execute
$post_backup_key = PostDataStore::getKeyForBackup($host, $bbs, $key, !empty($_REQUEST['newthread']));
PostDataStore::set($post_backup_key, $post);

echo '1';
exit;

// }}}

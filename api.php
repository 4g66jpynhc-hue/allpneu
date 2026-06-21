<?php
require_once 'config.php';
require_once 'auth.php';
requireAuth();

$table  = isset($_GET['table']) ? $_GET['table'] : '';
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? $_GET['id'] : null;
$all    = isset($_GET['all']);

$allowed = array('factures','devis','clients','ordres','catalogue');

// ── CONFIG ───────────────────────────────────────────────────
if ($table === 'config') {
    $db = getDB();
    if ($method === 'GET') {
        $rows = $db->query("SELECT cle, valeur FROM config")->fetchAll();
        $cfg = array();
        foreach ($rows as $r) $cfg[$r['cle']] = $r['valeur'];
        jsonResponse($cfg);
    }
    if ($method === 'POST') {
        $body = getBody();
        $stmt = $db->prepare("INSERT INTO config (cle, valeur) VALUES (?,?) ON DUPLICATE KEY UPDATE valeur=VALUES(valeur)");
        foreach ($body as $k => $v) {
            $val = is_array($v) ? json_encode($v) : $v;
            $stmt->execute(array($k, $val));
        }
        jsonResponse(array('ok' => true));
    }
    jsonError('Methode non supportee');
}

if (!in_array($table, $allowed)) jsonError("Table non autorisee", 403);

$db = getDB();

// Column mappings JS -> DB
$colMap = array(
    'devis'    => array('lines'=>'lignes','validite'=>'date_validite','date'=>'date_doc','devisRef'=>'devis_ref','factureRef'=>'facture_ref','section'=>'section_nom'),
    'factures' => array('lines'=>'lignes','echeance'=>'date_echeance','date'=>'date_doc','devisRef'=>'devis_ref','factureRef'=>'facture_ref'),
    'ordres'   => array('desc'=>'descrip','date'=>'date_doc','factureRef'=>'facture_ref'),
    'clients'  => array(),
    'catalogue'=> array('section'=>'section_nom'),
);
// DB -> JS
$revMap = array(
    'devis'    => array('lignes'=>'lines','date_validite'=>'validite','date_doc'=>'date','devis_ref'=>'devisRef','facture_ref'=>'factureRef','section_nom'=>'section'),
    'factures' => array('lignes'=>'lines','date_echeance'=>'echeance','date_doc'=>'date','devis_ref'=>'devisRef','facture_ref'=>'factureRef'),
    'ordres'   => array('descrip'=>'desc','date_doc'=>'date','facture_ref'=>'factureRef'),
    'clients'  => array(),
    'catalogue'=> array('section_nom'=>'section'),
);
$jsonFields = array('lignes','paiement');

function toJS($table, $row, $revMap, $jsonFields) {
    $rm = isset($revMap[$table]) ? $revMap[$table] : array();
    $out = array();
    foreach ($row as $k => $v) {
        $jsKey = isset($rm[$k]) ? $rm[$k] : $k;
        if (in_array($k, $jsonFields) && is_string($v) && $v !== '') {
            $decoded = json_decode($v, true);
            $v = $decoded !== null ? $decoded : array();
        }
        $out[$jsKey] = $v;
    }
    return $out;
}

function toDB($table, $body, $colMap, $jsonFields) {
    $cm = isset($colMap[$table]) ? $colMap[$table] : array();
    $out = array();
    foreach ($body as $k => $v) {
        if ($k === 'created_at') continue;
        $dbKey = isset($cm[$k]) ? $cm[$k] : $k;
        if (in_array($k, array('lines','paiement')) && is_array($v)) {
            $v = json_encode($v, JSON_UNESCAPED_UNICODE);
        }
        $out[$dbKey] = ($v === '') ? null : $v;
    }
    return $out;
}

// GET ALL
if ($method === 'GET' && !$id) {
    $order = in_array($table, array('factures','devis','ordres')) ? 'created_at DESC' : 'created_at ASC';
    $rows = $db->query("SELECT * FROM `$table` ORDER BY $order")->fetchAll();
    $out = array();
    foreach ($rows as $r) $out[] = toJS($table, $r, $revMap, $jsonFields);
    jsonResponse($out);
}

// GET ONE
if ($method === 'GET' && $id) {
    $st = $db->prepare("SELECT * FROM `$table` WHERE id=?");
    $st->execute(array($id));
    $row = $st->fetch();
    if (!$row) jsonError('Non trouve', 404);
    jsonResponse(toJS($table, $row, $revMap, $jsonFields));
}

// POST
if ($method === 'POST') {
    $body = getBody();
    if (empty($body['id'])) jsonError('id requis');
    $cols = toDB($table, $body, $colMap, $jsonFields);
    $names = implode(',', array_map(function($c){ return "`$c`"; }, array_keys($cols)));
    $ph = implode(',', array_fill(0, count($cols), '?'));
    $st = $db->prepare("INSERT IGNORE INTO `$table` ($names) VALUES ($ph)");
    $st->execute(array_values($cols));
    jsonResponse(array('ok' => true));
}

// PUT
if ($method === 'PUT') {
    if (!$id) jsonError('id requis');
    $body = getBody();
    $cols = toDB($table, $body, $colMap, $jsonFields);
    unset($cols['id']);
    unset($cols['created_at']);
    if (empty($cols)) jsonError('Aucune donnee');
    $set = implode(',', array_map(function($c){ return "`$c`=?"; }, array_keys($cols)));
    $vals = array_values($cols);
    $vals[] = $id;
    $st = $db->prepare("UPDATE `$table` SET $set WHERE id=?");
    $st->execute($vals);
    jsonResponse(array('ok' => true));
}

// DELETE
if ($method === 'DELETE') {
    if ($all) {
        $db->query("DELETE FROM `$table`");
    } else {
        if (!$id) jsonError('id requis');
        $st = $db->prepare("DELETE FROM `$table` WHERE id=?");
        $st->execute(array($id));
    }
    jsonResponse(array('ok' => true));
}

jsonError('Methode non supportee', 405);

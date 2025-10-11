<?php
// File: views/resources/delete.php

require_once __DIR__ . '/../../controllers/ResourceController.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$controller = new ResourceController();
$controller->destroy($id);

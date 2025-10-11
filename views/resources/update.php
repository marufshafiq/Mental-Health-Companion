<?php
// File: views/resources/update.php

require_once __DIR__ . '/../../controllers/ResourceController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$controller = new ResourceController();
$controller->update($id);

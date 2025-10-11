<?php
// File: views/resources/store.php

require_once __DIR__ . '/../../controllers/ResourceController.php';

$controller = new ResourceController();
$controller->store();

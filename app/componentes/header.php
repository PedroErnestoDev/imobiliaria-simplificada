<?php
// Session must start BEFORE ANY HTML output
// This file must be included at the very top of every page
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

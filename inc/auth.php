<?php
session_start();
function isLoggedIn() {
  return isset($_SESSION['user']);
}
function getUser() {
  return $_SESSION['user'] ?? null;
}
function isAdmin() {
  return isLoggedIn() && !empty($_SESSION['user']['is_admin']);
}
function requireLogin() {
  if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
  }
}
function requireAdmin() {
  if (!isAdmin()) {
    header('Location: index.php');
    exit();
  }
}
function login($user) {
  $_SESSION['user'] = $user;
}
function logout() {
  session_destroy();
}
function redirect($url) {
  header("Location: $url");
  exit();
}

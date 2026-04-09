<?php
//session_start();
require_once("../connexion/connexion.php");
// includes/header.php
// Variables attendues : $page_title, $active_page
$page_title  = $page_title  ?? 'EduManager';
$active_page = $active_page ?? '';

$login    = $_SESSION['login'] ?? 'Admin';
$role     = $_SESSION['role']  ?? 'Admin';
$initials = strtoupper(substr($login, 0, 2));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> — EduManager</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="wrapper">

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="logo">🎓</div>
        <div>
            <h1>EduManager</h1>
            <small>Gestion Scolaire</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Principal</div>
<?php 
if(isset($_SESSION['role']) && $_SESSION['role'] === 'Admin')
{
?>
        <a href="../tableau_de_bord/dashboard.php"
           class="nav-link <?php echo $active_page == 'dashboard' ? 'active' : ''; ?>">
            <span class="icon">🏠</span> Tableau de bord
        </a>
<?php
}
?>
        <div class="nav-section">Gestion</div>

        <a href="../eleves/liste_eleves.php"
           class="nav-link <?php echo $active_page == 'eleves' ? 'active' : ''; ?>">
            <span class="icon">👨‍🎓</span> Élèves
        </a>

        <a href="../enseignants/liste_enseignants.php"
           class="nav-link <?php echo $active_page == 'enseignants' ? 'active' : ''; ?>">
            <span class="icon">👨‍🏫</span> Enseignants
        </a>

        <a href="../classes/liste_classes.php"
           class="nav-link <?php echo $active_page == 'classes' ? 'active' : ''; ?>">
            <span class="icon">🏫</span> Classes
        </a>

        <a href="../matieres/liste_matieres.php"
           class="nav-link <?php echo $active_page == 'matieres' ? 'active' : ''; ?>">
            <span class="icon">📚</span> Matières
        </a>

        <div class="nav-section">Académique</div>

        <a href="../notes/liste_notes.php"
           class="nav-link <?php echo $active_page == 'notes' ? 'active' : ''; ?>">
            <span class="icon">📝</span> Notes
        </a>

        <a href="../absences/liste_absences.php"
           class="nav-link <?php echo $active_page == 'absences' ? 'active' : ''; ?>">
            <span class="icon">📅</span> Absences
        </a>

        <a href="../bulletins/bulletin_eleve.php"
           class="nav-link <?php echo $active_page == 'bulletins' ? 'active' : ''; ?>">
            <span class="icon">📊</span> Bulletins
        </a>

        <a href="../emploi_temps/liste_emploi.php"
           class="nav-link <?php echo $active_page == 'emploi_temps' ? 'active' : ''; ?>">
            <span class="icon">🕐</span> Emploi du temps
        </a>

        <div class="nav-section">Administration</div>

        <a href="../users/liste_users.php"
           class="nav-link <?php echo $active_page == 'users' ? 'active' : ''; ?>">
            <span class="icon">👤</span> Utilisateurs
        </a>

     <?php 
if(isset($_SESSION['role'])==='admin')
{
?>
     <a href="../paiements/liste_paiements.php"
           class="nav-link <?php echo $active_page == 'paiements' ? 'active' : ''; ?>">
            <span class="icon">💳</span> Paiements
        </a>
<?php
}
?>
        <a href="../parents/liste_parents.php"
           class="nav-link <?php echo $active_page == 'parents' ? 'active' : ''; ?>">
            <span class="icon">👨‍👩‍👧</span> Parents
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="../authentification/deconnexion.php" class="user-info" style="text-decoration:none">
            <div class="user-avatar"><?php echo $initials; ?></div>
            <div>
                <div class="user-name"><?php echo htmlspecialchars($login); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($role); ?> • Déconnexion</div>
            </div>
        </a>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <div class="topbar">
        <h2><?php echo $page_title; ?></h2>
        <div class="topbar-right"><?php echo date('d/m/Y'); ?></div>
    </div>
    <div class="content">

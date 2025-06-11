<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../api/config/database.php';

$database = new Database();
$pdo = $database->getConnection();

// Déterminer l'action selon la méthode HTTP et les paramètres
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';
} else {
    $action = $_GET['action'] ?? 'status';
}

switch ($action) {
    
    case 'login':
        // Connexion utilisateur
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée. Utilisez POST."]);
            exit;
        }
        
        $email = trim($_POST['mail'] ?? '');
        $motdepasse = $_POST['motdepasse'] ?? '';
        $messageErreur = '';
        
        // Validation basique
        if (empty($email) || empty($motdepasse)) {
            $messageErreur = 'Veuillez remplir tous les champs obligatoires.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messageErreur = 'Adresse email invalide.';
        } else {
            try {
                // Rechercher l'utilisateur par email
                $stmt = $pdo->prepare("SELECT id, mdp, nom, prenom FROM Personne WHERE email = :email");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // if ($user && password_verify($motdepasse, $user['mdp'])) {
                //     // Connexion réussie
                //     $_SESSION['user_id'] = $user['id'];
                //     $_SESSION['user_email'] = $email;
                //     $_SESSION['user_nom'] = $user['nom'];
                //     $_SESSION['user_prenom'] = $user['prenom'];
                //     $_SESSION['logged_in'] = true;
                    
                //     // Redirection vers la page d'accueil
                //     header('Location: accueil.php');
                //     exit;
                // } else {
                //     $messageErreur = 'Email ou mot de passe incorrect.';
                // }
                if ($user) {
                    $hashEnBase = $user['mdp'];
                    
                    if (
                        // Cas normal : mot de passe hashé
                        password_verify($motdepasse, $hashEnBase)
                        ||
                        // Cas temporaire : mot de passe stocké en clair
                        $motdepasse === $hashEnBase
                    ) {
                        // Connexion réussie
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_nom'] = $user['nom'];
                        $_SESSION['user_prenom'] = $user['prenom'];
                        $_SESSION['logged_in'] = true;

                        // Optionnel : mettre à jour le mot de passe en base avec le hash
                        if ($motdepasse === $hashEnBase) {
                            $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
                            $update = $pdo->prepare("UPDATE Personne SET mdp = :mdp WHERE id = :id");
                            $update->execute(['mdp' => $hash, 'id' => $user['id']]);
                        }

                        header('Location: accueil.php');
                        exit;
                    } else {
                        $messageErreur = 'Email ou mot de passe incorrect.';
                    }
                }

                
            } catch (PDOException $e) {
                error_log("Erreur de connexion à la base de données : " . $e->getMessage());
                $messageErreur = 'Erreur de connexion à la base de données.';
            }
        }
        
        // Si on arrive ici, il y a eu une erreur
        $_SESSION['messageErreur'] = $messageErreur;
        header('Location: connexion.php');
        exit;
        break;
        
    case 'logout':
        // Déconnexion utilisateur
        $_SESSION = array();
        
        // Détruire le cookie de session s'il existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Rediriger vers la page de connexion
        header('Location: connexion.php');
        exit;
        break;
        
    case 'status':
        // Vérifier le statut de connexion (pour AJAX)
        header("Content-Type: application/json; charset=UTF-8");
        
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            echo json_encode([
                'logged_in' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'email' => $_SESSION['user_email'],
                    'nom' => $_SESSION['user_nom'],
                    'prenom' => $_SESSION['user_prenom']
                ]
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        break;
        
    case 'register':
        // Inscription utilisateur (optionnel)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(["error" => "Méthode non autorisée. Utilisez POST."]);
            exit;
        }
        
        header("Content-Type: application/json; charset=UTF-8");
        
        $email = trim($_POST['email'] ?? '');
        $motdepasse = $_POST['motdepasse'] ?? '';
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        
        // Validation
        if (empty($email) || empty($motdepasse) || empty($nom) || empty($prenom)) {
            echo json_encode(["error" => "Tous les champs sont obligatoires."]);
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["error" => "Adresse email invalide."]);
            exit;
        }
        
        // Vérifier si l'email existe déjà
        try {
            $stmt = $pdo->prepare("SELECT id FROM Personne WHERE email = :email");
            $stmt->execute(['email' => $email]);
            
            if ($stmt->fetch()) {
                echo json_encode(["error" => "Cette adresse email est déjà utilisée."]);
                exit;
            }
            
            // Hasher le mot de passe
            $password_hash = password_hash($motdepasse, PASSWORD_DEFAULT);
            
            // Insérer le nouvel utilisateur
            $stmt = $pdo->prepare("INSERT INTO Personne (email, mdp, nom, prenom) VALUES (:email, :mdp, :nom, :prenom)");
            $stmt->execute([
                'email' => $email,
                'mdp' => $password_hash,
                'nom' => $nom,
                'prenom' => $prenom
            ]);
            
            echo json_encode(["success" => "Inscription réussie."]);
            
        } catch (PDOException $e) {
            error_log("Erreur d'inscription : " . $e->getMessage());
            echo json_encode(["error" => "Erreur lors de l'inscription."]);
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(["error" => "Action non reconnue."]);
        break;
}
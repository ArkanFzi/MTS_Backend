<?php  
$modules = [  
    'Auth' => ['Controllers', 'Requests', 'Services'],  
    'Common/Explore' => ['Controllers', 'Services'],  
    'Admin/Badge' => ['Controllers', 'Requests', 'Services', 'Repositories'],  
    'Admin/Category' => ['Controllers', 'Requests', 'Services', 'Repositories'],  
    'Admin/Tag' => ['Controllers', 'Services', 'Repositories'],  
    'Admin/RoleManagement' => ['Controllers', 'Services'],  
    'Moderator/Report' => ['Controllers', 'Requests', 'Services', 'Repositories'],  
    'Moderator/UserSanction' => ['Controllers', 'Services', 'Repositories'],  
    'User/Post' => ['Controllers', 'Requests', 'Services', 'Repositories'],  
    'User/Comment' => ['Controllers', 'Requests', 'Services', 'Repositories'],  
    'User/Interaction' => ['Controllers', 'Services', 'Repositories'],  
    'User/Notification' => ['Controllers', 'Services'],  
    'User/Gamification' => ['Controllers', 'Services']  
];  
foreach ($modules as $module => $subFolders) {  
    foreach ($subFolders as $sub) {  
        $path = "app/Modules/{$module}/{$sub}";  
        if (!is_dir($path)) {  
            mkdir($path, 0755, true);  
            echo "Created: {$path}\n";  
        }  
    }  
} 

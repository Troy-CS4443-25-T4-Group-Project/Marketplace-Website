<?php
$page_title = 'About Us';

// Include necessary files
require_once 'includes/display_helpers.php';

// Team members data - structured array for better organization
$team_members = [
    [
        'name' => 'Austin Cain',
        'image' => 'images/team/austin.jpg',
        'bio' => 'Austin Cain is a 23 year old Computer Science senior who loves video games. He wants to work in the game development industry after completing his degree.'
    ],
    [
        'name' => 'Phoenix Hussey',
        'image' => 'images/team/phoenix.jpg'
        'bio' => 'Phoenix Hussey is a senior in Computer Science. He is currently pursuing the Aviation field for a commercial pilot\'s license and A&P with the FAA. He is currently employed as a structural repairer for the Alabama Army National Guard and wants to pursue aviation full time with a degree in Computer Science to boost his resume.'
    ],
    [
        'name' => 'James Ward',
        'image' => 'images/team/james.jpg',
        'bio' => 'James is a senior at Troy University majoring in Computer Science. His hope is to take the skills he has learned and apply them to a career in Information Technology'
    ],
    [
        'name' => 'Teresa Williams',
        'image' => 'images/team/teresa.jpg',
        'bio' => 'Teresa is a Senior at Troy University majoring in Computer Science. She currently works as a Nuclear Electrician but desires some day to work in the CyberSecurity field.'
    ]
    // Hidden team members (absent from project)
    // ['name' => 'Brandon Horn', 'image' => '', 'bio' => 'Description Pending.'],
    // ['name' => 'Rhett Parker', 'image' => '', 'bio' => 'Description Pending.']
];

// Include the header
include 'includes/header.php';

// Define breadcrumbs for this page
$breadcrumbs = [
    ['text' => 'Home', 'url' => 'index.php'],
    ['text' => 'About us']
];

// Display breadcrumbs
echo renderBreadcrumbs($breadcrumbs);
?>

<div class="main-content-wrapper">
    <div class="content">
        <?php foreach ($team_members as $member): ?>
        <div class="teamMemberInfo">
            <figure class="memberImage">
                <figcaption><?= htmlspecialchars($member['name']); ?></figcaption>
                <img src="<?= !empty($member['image']) ? $member['image'] : 'images/team/placeholder.jpg'; ?>" 
                     alt="<?= htmlspecialchars($member['name']); ?>"/>
            </figure>
            <p class="memberInfo"><?= htmlspecialchars($member['bio']); ?></p>
        </div>
        <br>
        <?php endforeach; ?>
    </div> <!-- end content wrapper div -->
</div>

<?php
// Include the footer
include 'includes/footer.php';
?>

<?php

use \Toolkit\Validation\{
    NameValidator,
    EmailValidator,
    NumberValidator,
    ValidatorSet
};


require __DIR__ . '/../vendor/autoload.php';

if ($_POST) {
    $validators = new ValidatorSet();

    $name = new NameValidator($_POST['name'], true);
    $name->set_range(5, 15);
    $validators->add($name, 'name');

    $validators->add(
            new EmailValidator($_POST['email'], true), 'email'
    );

    $age = new NumberValidator($_POST['age']);
    $age->set_range(18, 25);
    $validators->add($age, 'age');

    $errors = [];
    foreach ($validators as $key => $validator) {
        ${$key} = $validator->has_error() ? $validator->get_error() : '';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Form Test</title>
</head>
<body>

<form method="post" novalidate>
    <p>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="name">
        <?= $name ?? ''; ?>
    </p>
    <p>
        <label for="email">Email address:</label>
        <input type="email" id="email" name="email" placeholder="email">
        <?= $email ?? ''; ?>
    </p>
    <p>
        <label for="age">Age:</label>
        <input type="number" id="age" name="age" placeholder="age">
        <?= $age ?? ''; ?>
    </p>
    <p>
        <input type="submit" value="Submit">
    </p>
</form>

<?php if (! $validators->get_errors()) : ?>
    <p>Your form was successfully submitted.</p>
<?php endif; ?>
</body>
</html>
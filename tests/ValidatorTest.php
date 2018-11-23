<?php

use \Toolkit\Validation\{
    EmailValidator,
    NameValidator,
    NumberValidator,
    ValidatorSet
};

require __DIR__ . '/../vendor/autoload.php';


class ValidatorTest extends PHPUnit_Framework_TestCase {

    public function test_email_fail()
    {
        $email = new EmailValidator('notanemail');
        $this->assertTrue($email->has_error());
    }

    public function test_email_success()
    {
        $email = new EmailValidator('me@here.com');
        $this->assertFalse($email->has_error());
    }

    public function test_email_required()
    {
        $email = new EmailValidator('', true);
        $this->assertEquals('This is a required field', $email->get_error());
    }

    public function test_name_fail_too_many_characters_default25()
    {
        $name = new NameValidator('toomanycharacters-------26');
        $this->assertTrue($name->has_error());
    }

    public function test_name_success()
    {
        $name = new NameValidator('sufficientcharacters---25');
        $this->assertFalse($name->has_error());
    }

    public function test_name_set_max_chars_fail()
    {
        $name = new NameValidator('123456');
        $name->set_range(1, 5);
        $this->assertEquals('Must be between 1 and 5 characters (inclusive)', $name->get_error());
    }

    public function test_name_set_min_chars_fail()
    {
        $name = new NameValidator('123');
        $name->set_range(4, 5);
        $this->assertEquals('Must be between 4 and 5 characters (inclusive)', $name->get_error());
    }

    public function test_name_required()
    {
        $age = new NameValidator('', true);

        $this->assertEquals('This is a required field', $age->get_error());
    }

    public function test_age_fail_too_low_default21()
    {
        $age = new NumberValidator('20');

        $this->assertTrue($age->has_error());
    }

    public function test_age_fail_too_high_default44()
    {
        $age = new NumberValidator('45');

        $this->assertTrue($age->has_error());
    }

    public function test_age_success_low_boundary()
    {
        $age = new NumberValidator('21');

        $this->assertFalse($age->has_error());
    }

    public function test_age_success_high_boundary()
    {
        $age = new NumberValidator('44');

        $this->assertFalse($age->has_error());
    }

    public function test_age_success_set_low_boundary()
    {
        $age = new NumberValidator('5');

        $age->set_range(4, 6);

        $this->assertFalse($age->has_error());
    }

    public function test_age_fail_set_low_boundary()
    {
        $age = new NumberValidator('3');

        $age->set_range(4, 6);

        $this->assertEquals('Integer value from 4 to 6', $age->get_error());
    }

    public function test_age_success_set_high_boundary()
    {
        $age = new NumberValidator('10');

        $age->set_range(4, 10);

        $this->assertFalse($age->has_error());
    }

    public function test_age_fail_set_high_boundary()
    {
        $age = new NumberValidator('10');

        $age->set_range(4, 9);

        $this->assertEquals('Integer value from 4 to 9', $age->get_error());
    }

    public function test_age_required()
    {
        $age = new NumberValidator('', true);

        $this->assertEquals('This is a required field', $age->get_error());
    }

    public function test_set()
    {
        $val_set = new ValidatorSet();

        $val_set->add(new EmailValidator('notanemail'), 'email');
        $email = $val_set->item('email');

        $this->assertTrue($email->has_error());
    }

    public function test_set_return_errors()
    {
        $val_set = new ValidatorSet();

        $val_set->add(new EmailValidator('notanemail'), 'email1');
        $val_set->add(new EmailValidator('me@here.com'), 'email2');

        $expected = ['email1'=>'Not a valid email address'];

        $this->assertEquals($expected, $val_set->get_errors());
    }
}

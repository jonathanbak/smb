README
======

What is SMB?
------------

SMB�� PHP ���ø����� ������ configure.json ���� ������ ���� ����Ʈ�� ��Ҽ� �ְ� �����ִ� ����Ʈ ���� ���ø��Դϴ�.
�̹� ������ PHP ���ø�(CI, Zend Framework, laravel��)���� ������ �Ź� �ش� ���ø��� ������ ���� �ǹ��� Ȱ���ϴ°� ���� ���ŷο� ���� �ƴϱ⿡
���� ��ġ�� JSON�������� �� ȯ�漳�� ���ϸ� �������༭ DDD����� ���߻���Ʈ ��� ���� ���̽��� ����ְ� �������� PHP Ŭ������ ���� �Ը��� �°� �����Ͽ�
����ϰ� �ϴ°� ��� �; ����� ���ҽ��ϴ�. (��� �̷��� ���ٺ��� ���� ã�� ������ ��ɵ��� �ᱹ ���̺귯��ȭ �ϰ� �ǰ�������)

Features
--------

SMB supports the following:

* *PHP 5.3 ȣȯ, �Ϻα���� ������� ���Ҽ� ������ �⺻ ����� �̻���� �۵��մϴ�. 
* *PHP 5.4 ���� �Ϻ��ϰ� �۵��մϴ�. (Ŭ������ 5.4���� �����Ӱ� ����� �����ϴ�����) 

Requirements
------------

phpDocumentor requires the following:

* PHP 5.3 or higher
* Composer - Dependency Management for PHP
* zend-config, https://github.com/zendframework/zend-config
* zend-db 2.0 or higher, https://github.com/zendframework/zend-db

**Note:**
php composer �� ��ġ�ϰ� SMB�� �߰��ϸ� �ڵ����� ������ ���̺귯������ ��ġ�մϴ�.

Installation
------------

1. Download and install Composer by following the [official instructions](https://getcomposer.org/download/).
2. Create a composer.json defining your dependencies. Note that this example is
a short version for applications that are not meant to be published as packages
themselves. To create libraries/packages please read the
[documentation](https://getcomposer.org/doc/02-libraries.md).

    ``` json
    {
        "require": {
            ""jonathanbak/smb":"~1.0"
        }
    }
    ```

3. Run Composer: `php composer.phar install`
4. Browse for more packages on [Packagist](https://packagist.org).

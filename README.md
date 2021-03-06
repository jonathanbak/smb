README
======

이 프로젝트는 더이상 사용하지 않습니다.

[Php Simple Framework](https://github.com/jonathanbak/psf) 로 다시 시작합니다.


What is SMB?
------------

SMB는 PHP 템플릿으로 간단한 configure.json 설정 만으로 다중 사이트를 운영할수 있게 도와주는 사이트 관리 템플릿입니다.
이미 유명한 PHP 템플릿(CI, Zend Framework, laravel등)들이 많지만 매번 해당 템플릿의 문법을 배우고 실무에 활용하는게 여간 번거로운 일이 아니기에
최초 설치시 JSON문법으로 된 환경설정 파일만 셋팅해줘서 DDD기반의 다중사이트 운영을 위한 베이스만 잡아주고 나머지는 PHP 클래스를 각자 입맛에 맞게 셋팅하여
사용하게 하는게 어떨까 싶어서 만들어 보았습니다. (사실 이러고 쓰다보면 자주 찾는 유용한 기능들은 결국 라이브러리화 하게 되겠지만요)

Features
--------

SMB supports the following:

* *PHP 5.3 호환, 일부기능을 사용하지 못할수 있으나 기본 기능은 이상없이 작동합니다. 
* *PHP 5.4 에서 완벽하게 작동합니다. (클로저가 5.4부터 자유롭게 사용이 가능하더라고요) 

Requirements
------------

phpDocumentor requires the following:

* PHP 5.3 or higher
* Composer - Dependency Management for PHP
* zend-config, https://github.com/zendframework/zend-config
* zend-db 2.0 or higher, https://github.com/zendframework/zend-db

**Note:**
php composer 를 설치하고 SMB를 추가하면 자동으로 의존성 라이브러리들을 설치합니다.

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
            "jonathanbak/smb":"~1.0"
        }
    }
    ```

3. Run Composer: `php composer.phar install`
4. Browse for more packages on [Packagist](https://packagist.org).


Folder Structure
-------------------

SMB 사용시 추천하는 폴더 구조는 아래와 같습니다. 

    .
    ├── app
    │   ├── _tmp            # 임시폴더, 캐쉬파일과 로그 생성
    │   ├── controllers     # URL에서 접근하는 controller 파일
    │   ├── models          # 모델 파일, 주요 로직
    │   └── views           # View 폴더
    │       ├── css             # css 파일
    │       ├── images          # images 파일
    │       ├── js              # javascript 파일
    │       └── tpl             # tpl 파일 (html 파일)
    ├── config              # 설정 파일
    │   ├── db              # DB 정보 설정 파일
    │   └── site            # 사이트 설정 파일
    ├── html                # 실제 웹서버의 DOCUMENT_ROOT
    └── vendor              # Composer 라이브러리 폴더
    
**app 폴더 하위 구조**는 *config/site/usersiteurl.json 파일안에서 별도 정의가 가능합니다.*
# Documentation - Honeypot - Group  17

## Table of contents

1. [Web environment](#web-environment)
2. [Anisble scripts](#ansible-scripts)
3. [Logging environment](#logging-environment)

### Groupmembers

- Liam Delagrense
- Mats Fiers
- Pieter Verheye
## Web environment

### Technologies
The web environment uses a fairly conventional LAMP stack with the following versions:
- Debian 11
- Apache 2.4.54
- MariaDB 10.5.15
- PHP 8.1.13

### User authentication
Users can register and login on the website to access the challenges. 
User passwords are stored using the cryptographically secure PHP password_hash() function, by default this uses bcrypt and is salted. 
For added security we have increased the cost from 10 to 14 (default is 10). This means that the password hashing function will take longer to compute, making brute force attacks more difficult.

The users are authenticated by their Session ID, which stays valid for 7 after their last action ot the site. 
If the period between use is longer than 7 days the user wil have to log in again. 
This is when the submitted password is checked against the stored hash using the PHP password_verify() function.
If the password is correct the user is logged in and a new Session ID is stored.

### Admin panel
The admin panel uses a completely separate authentication method.

When accessing the admin panel the server will send an "Authentication Required" message in the response header to the client browser causing it to pop up a Username/Password input window.
The username and password are essentially hardcoded in the php file, which is not ideal, but it is a simple solution that works, and serves the purpose of this project.

Once logged in the admin panel will show a list of all users, when they registered, when they last made a request and which challenges they have solved. From here we can also enable/disable users or delete them all together.
### Challenges

Find the explanation of the challenges and how to solve them below.

### Challenge 1

---
Exploit a vulnerability in PHP to log in as admin despite not knowing the password.
#### Solution

In PHP, the following code will return true:

```php
if ("0e12345" == "0e54321") {
    echo "true";
}
```
This happens because the == (2x equal sign instead of 3x) operator will compare the values of the two operands, but not their type. In this case, both "0e12345" and "0e54321" are read as 0 in scientific notation. Since 0 == 0, the code will return true.

Now all we have to do is find a string which hashes `0e` followed by only numeric characters. We could write a simple script to do this, but we can also find examples in [this](https://github.com/spaze/hashes) repository.

Since our implementation uses md5 hashing the following plain text string would work `QLTHNDT`.


### Challenge 2

---
Exploit this vulnerability and get SSRF to work. SSRF usually poses many risks to the server such as port scanning, reading files, and even executing commands. In this challenge that not possible as to protect the server, however a valid attempt is enough to solve the challenge. 

#### Solution

SSRF flaws occur whenever a web application is fetching a remote resource without validating the user-supplied URL.

The button fetches the contents of a text file on the webserver, when exploiting you can change the destination url of the fetch. This would theoretically allow you to fetch any file on the webserver, make http(s) connections, etc. 

When the button is clicked the URL will update to show our get request [http://group17web.hp.ti.howest.be/challenges/2-c81e728d9d4c2f636f067f89cc14862c.php?getData=./chall2-data.txt](http://group17web.hp.ti.howest.be/challenges/2-c81e728d9d4c2f636f067f89cc14862c.php?getData=./chall2-data.txt) and display the data found in chall2-data.txt. 

By changing the `getData` parameter to `file:///etc/passwd` we would be able to fetch the contents of the passwd file if this was not accounted for.
Since it is accounted for, this doesn't work, but you would have solved another challenge.

Full link for possible solution: [http://group17web.hp.ti.howest.be/challenges/2-c81e728d9d4c2f636f067f89cc14862c.php?getData=file:///etc/passwd](http://group17web.hp.ti.howest.be/challenges/2-c81e728d9d4c2f636f067f89cc14862c.php?getData=file:///etc/passwd)

### Challenge 3

---
Upload an image to the server and get xxs to execute.

#### Solution

There's 3 allowed file types: jpeg, png and svg. Jpeg and png are not used for xxs, so we'll focus on svg.

SVG's use xml, so we can use xxs in the xml tags.
when opening a typical svg file in a text editor, you'll see something along the lines of this:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
  "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg xmlns="http://www.w3.org/2000/svg" version="1.1"
     width="120" height="120">
  <rect x="14" y="23" width="200" height="50" fill="lime"
      stroke="black" />
</svg>
```


By simply using a script tag in the svg file, we can execute xxs. The code inside the script tag will be executed when the svg is opened in a browser.

This is an example of a svg file that will execute xxs:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
  "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg xmlns="http://www.w3.org/2000/svg" version="1.1"
     width="120" height="120">
  <rect x="14" y="23" width="200" height="50" fill="lime"
      stroke="black" />
    <script type="text/javascript">
        alert("Get XXS'ed on lmao");
    </script>
</svg>
```
It is good to note that the xss would not execute if the svg was displayed in an img tag, but since the images on this page are displayed in an iframe, it will.

### Challenge 4

---
When loading this page you'll get a message saying that you are not allowed on that page. Find a way to bypass this restriction.

#### Solution

When you first load in the page a cookie will be set with the name `is Admin` and the value `false`. When you try to access the page again, the cookie will be checked and if the value is `false` you will be show the same error.

All it takes to solve this challenge is to change the value of the cookie to `true`. This can be done by using the developer tools in your browser.

### Challenge 5

---
Unlike the previous challenges you do not get a link to the challenge page. The challenge is to find the page yourself.

#### Solution

It only makes sense that challenge 5 follows the same naming structure as the other challenges.
We will start by observing their names:
- Challenge 1: `1-c4ca4238a0b923820dcc509a6f75849b.php`
- Challenge 2: `2-c81e728d9d4c2f636f067f89cc14862c.php`
- Challenge 3: `3-eccbc87e4b5ce2fe28308fd9f2a7baf3.php`
- Challenge 4: `4-a87ff679a2f3e71d9181a67b7542122c.php`

We can see that the first part of the name is the challenge number, and the second part is the md5 hash of the challenge number. This means that the name of challenge 5 is `5-` followed by the md5 hash of 5, resulting in `5-e4da3b7fbbce2345d7772b0674a318d5.php`

Solve the challange by going to [http://group17web.hp.ti.howest.be/challenges/5-e4da3b7fbbce2345d7772b0674a318d5.php](http://group17web.hp.ti.howest.be/challenges/5-e4da3b7fbbce2345d7772b0674a318d5.php)

## Ansible scripts
Find the ansible scripts used to deploy the webserver [here](https://git.ti.howest.be/TI/2022-2023/s3/websecurity-and-honeypot/students/group-17/ansible)

## Logging environment
Unfortunately, due to server issues we were unable to get a publicly accessible ELK server up and running. We did make this locally on our own VM.
In there we have everything one would need to analyze the logs, including a Kibana dashboard.
# Vacuum Robot

This is the repository holding the code for our new product - the MyQ Vacuum Robot!

## How to execute the program
Robot is build using PHP 7.2 so you are going to need this binary installed in your system to execute the program. If you have it already follow the steps below:
 1. Clone the repo or download the zip file
 2. Launch your favorite terminal and navigate to the source folder of the app
 3. Execute `php cleaning_robot.php <source.json> <result.json>`

If you don't have PHP installed and you don't want to install it or you have a different version installed - worry not; You can use docker to run it (provided that you have it installed as well)

`docker run -it --rm --name=myqroomba -v "$PWD":/usr/src/myapp -w /usr/src/myapp php:7.2-cli php cleaning_robot.php <source.json> <result.json>`

Note: Yes, vendor folder should be not included in the repo. Since it is a test app though and the vendor libs folder is tiny, it is included for convenience.
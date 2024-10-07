# KAES - Kabarak Alumni Engagement System

KAES is a web-based platform designed to facilitate engagement between Kabarak University alumni and current students. The platform enables students to connect with alumni from the same school and course, fostering mentorship, career guidance, and professional networking.

## Features

### For Students:
- **Student Registration**: Register with personal details including name, email, phone, school, course, year of study, etc.
- **Login**: Students can log in using either their username or email along with their password.
- **Alumni Recommendations**: Students receive recommendations of alumni from the same school and course.
- **Profile Management**: Update student profiles and view recommended alumni.
- **Messaging**: Contact alumni for mentorship and career advice.

### For Alumni:
- **Alumni Registration**: Alumni can register with details such as name, email, phone, school, course, employment status, and year of graduation.
- **Login**: Alumni can log in using either their username or email along with their password.
- **Student Recommendations**: Alumni receive recommendations of students from the same school and course who might benefit from mentorship.
- **Profile Management**: Update profile information and connect with students for mentorship.

### Common Features:
- **Unified Dashboard**: Both students and alumni have access to the same homepage with customized content relevant to their roles.
- **Networking & Messaging**: Both groups can search, connect, and communicate within the platform.
- **Secure Authentication**: Secure login functionality with hashed passwords for both alumni and students.

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript, Bootstrap
- **Backend**: PHP, MySQL
- **Database**: MySQL for managing student and alumni data
- **Version Control**: GitHub

## Setup and Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/your-username/kaes.git
    ```

2. Navigate to the project directory:
    ```bash
    cd kaes
    ```

3. Set up the database:
    - Create a MySQL database named `kaes`.
    - Import the provided SQL file to set up the required tables.

4. Configure database connection:
    - Open the `db.php` file and update the database credentials to match your local environment.

5. Run the project:
    - Make sure you have a local server running (e.g., XAMPP or WAMP).
    - Place the project folder in the server directory (e.g., `htdocs` for XAMPP).
    - Open your browser and navigate to `http://localhost/kaes`.

## Contribution Guidelines
Feel free to fork this repository and submit pull requests. If you find any issues or have feature requests, please open an issue on GitHub.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.


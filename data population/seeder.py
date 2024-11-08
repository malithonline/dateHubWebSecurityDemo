import random
from datetime import datetime, timedelta
import bcrypt
import requests
import os
import time
from pathlib import Path

# Create uploads directory if it doesn't exist
UPLOAD_DIR = Path('uploads')
UPLOAD_DIR.mkdir(exist_ok=True)

# Sri Lankan names data
sinhala_first_names_male = [
    'Kasun', 'Chamara', 'Nuwan', 'Prasad', 'Sampath', 'Buddhika', 'Lalith', 'Saman',
    'Nimal', 'Ruwan', 'Pradeep', 'Chandana', 'Thilina', 'Dimuth', 'Dasun', 'Lahiru',
    'Isuru', 'Charith', 'Dinesh', 'Thisara'
]

sinhala_first_names_female = [
    'Malini', 'Kumari', 'Chamari', 'Dilini', 'Sachini', 'Hashini', 'Sanduni', 'Chathurika',
    'Nilmini', 'Deepika', 'Madhavi', 'Thilini', 'Sewwandi', 'Dulanjali', 'Imalka', 'Sachini',
    'Hansika', 'Nimasha', 'Kavindi', 'Nethmi'
]

sinhala_last_names = [
    'Perera', 'Silva', 'Fernando', 'Dissanayake', 'Bandara', 'Rathnayake', 'Wickramasinghe',
    'Gunawardena', 'Rajapaksa', 'Senanayake', 'Jayawardena', 'Kulasekara', 'Mendis',
    'Herath', 'Gunasekara', 'Amarasinghe', 'Karunaratne', 'Pathirana', 'Weerasinghe'
]

tamil_first_names_male = [
    'Rajan', 'Kumar', 'Suresh', 'Ramesh', 'Ganesh', 'Mahesh', 'Dinesh', 'Rajesh',
    'Kamal', 'Vijay', 'Arun', 'Prakash', 'Siva', 'Krishna', 'Tamil', 'Selvan'
]

tamil_first_names_female = [
    'Priya', 'Lakshmi', 'Devi', 'Kala', 'Shanthi', 'Uma', 'Geetha', 'Meena',
    'Kamala', 'Vimala', 'Malathi', 'Thulasi', 'Revathi', 'Selvi', 'Kavitha'
]

tamil_last_names = [
    'Rajah', 'Pillai', 'Nadarajah', 'Sivakumar', 'Chandran', 'Yoganathan', 
    'Balakrishnan', 'Mahendran', 'Sivananthan', 'Rajaratnam'
]

sl_cities = [
    'Colombo', 'Kandy', 'Galle', 'Jaffna', 'Negombo', 'Batticaloa',
    'Trincomalee', 'Anuradhapura', 'Matara', 'Kurunegala', 'Badulla'
]

interests_list = [
    'Cricket', 'Buddhism', 'Tamil Movies', 'Sinhala Music', 'Traditional Dancing',
    'Cooking Sri Lankan Food', 'Beach Activities', 'Temple Visiting', 'Photography',
    'Traveling', 'Reading', 'Hiking', 'Volleyball', 'Carrom', 'Elle'
]

def download_ai_face():
    """Download a face from thispersondoesnotexist.com"""
    url = "https://thispersondoesnotexist.com"
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    }
    try:
        response = requests.get(url, headers=headers)
        if response.status_code == 200:
            return response.content
        return None
    except:
        return None

def generate_sql_file(num_users):
    sql_filename = 'dating_app_data.sql'
    image_files = []

    with open(sql_filename, 'w', encoding='utf-8') as sql_file:
        # Write header with correct order of operations
        sql_file.write("""-- Dating App Database Population
USE aaa_system;

-- Delete existing data in correct order (child tables first)
DELETE FROM activity_logs;
DELETE FROM matches;
DELETE FROM users;

-- Start inserting new data
""")

        # Generate users
        user_ids = []
        for i in range(1, num_users + 1):
            try:
                # Download and save AI generated face
                print(f"Downloading face image {i}/{num_users}...")
                face_data = download_ai_face()
                if face_data:
                    image_filename = f'profile_{i}.jpg'
                    image_path = UPLOAD_DIR / image_filename
                    with open(image_path, 'wb') as f:
                        f.write(face_data)
                    image_files.append(image_path)
                    time.sleep(1)  # Be nice to the API
                
                gender = random.choice(['male', 'female'])
                ethnicity = random.choice(['sinhala', 'tamil'])
                
                # Generate name based on ethnicity and gender
                if ethnicity == 'sinhala':
                    first_name = random.choice(sinhala_first_names_male if gender == 'male' else sinhala_first_names_female)
                    last_name = random.choice(sinhala_last_names)
                else:
                    first_name = random.choice(tamil_first_names_male if gender == 'male' else tamil_first_names_female)
                    last_name = random.choice(tamil_last_names)

                username = f"{first_name.lower()}_{last_name.lower()}_{random.randint(100,999)}"
                email = f"{username}@example.com"
                
                # Generate birth date
                years_ago = random.randint(18, 40)
                birth_date = datetime.now() - timedelta(days=years_ago*365)
                birth_date_str = birth_date.strftime('%Y-%m-%d')

                profile_photo = f'uploads/{image_filename}' if face_data else 'NULL'
                
                location = random.choice(sl_cities)
                interests = ', '.join(random.sample(interests_list, random.randint(3, 6)))
                looking_for = random.choice(['male', 'female', 'both'])
                
                # Generate bio
                bio = random.choice([
                    f"Hi, I'm {first_name}! Looking for meaningful connections.",
                    f"{first_name} here, enjoying life in {location}.",
                    f"Simple person with simple dreams. From {location}.",
                    f"Love to explore new places and meet new people.",
                    f"Looking for someone who shares my interests in {interests}."
                ]).replace("'", "''")  # Escape single quotes for SQL

                # Hash password (password123 for all users)
                hashed_password = bcrypt.hashpw('password123'.encode('utf-8'), bcrypt.gensalt())
                password_hash = hashed_password.decode('utf-8')

                # Write user INSERT statement
                sql_file.write(f"""
INSERT INTO users (username, password, email, gender, birth_date, location, interests, bio, looking_for, profile_photo)
VALUES (
    '{username}',
    '{password_hash}',
    '{email}',
    '{gender}',
    '{birth_date_str}',
    '{location}',
    '{interests}',
    '{bio}',
    '{looking_for}',
    '{profile_photo}'
);
""")
                user_ids.append(i)

            except Exception as e:
                print(f"Error generating user {i}: {e}")
                continue

        # Add a delay to ensure all users are inserted
        sql_file.write("\n-- Ensure all users are inserted before creating matches\n")
        
        # Generate matches
        for user_id in user_ids:
            num_matches = random.randint(1, 5)
            potential_matches = [uid for uid in user_ids if uid != user_id]
            if potential_matches:
                matches = random.sample(potential_matches, min(num_matches, len(potential_matches)))
                for matched_id in matches:
                    match_time = datetime.now() - timedelta(days=random.randint(0, 30))
                    sql_file.write(f"""
INSERT INTO matches (user_id, liked_user_id, created_at)
SELECT {user_id}, {matched_id}, '{match_time.strftime('%Y-%m-%d %H:%M:%S')}'
FROM users WHERE EXISTS (
    SELECT 1 FROM users WHERE id = {user_id}
) AND EXISTS (
    SELECT 1 FROM users WHERE id = {matched_id}
);
""")

        # Generate activity logs
        for user_id in user_ids:
            num_logs = random.randint(1, 10)
            for _ in range(num_logs):
                action = random.choice(['login', 'logout', 'profile_update', 'match_created'])
                log_time = datetime.now() - timedelta(days=random.randint(0, 30))
                sql_file.write(f"""
INSERT INTO activity_logs (user_id, action, timestamp)
SELECT {user_id}, '{action}', '{log_time.strftime('%Y-%m-%d %H:%M:%S')}'
FROM users WHERE EXISTS (
    SELECT 1 FROM users WHERE id = {user_id}
);
""")

        # Add final commit
        sql_file.write("\nCOMMIT;\n")

    print(f"SQL file '{sql_filename}' has been generated successfully!")
    print(f"Generated {len(image_files)} profile images in the 'uploads' directory")
    print("Note: All users have password: 'password123'")

if __name__ == "__main__":
    try:
        num_users = int(input("Enter the number of users to generate (recommended 20-50): "))
        generate_sql_file(num_users)
    except ValueError:
        print("Please enter a valid number")
    except Exception as e:
        print(f"An error occurred: {e}")

import mysql.connector
from twilio.rest import Client

# Database configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'airline_db'
}

# Twilio configuration
twilio_account_sid = 'ACa8df90507d49a51c6364f45f3b043664'
twilio_auth_token = '43ce2d841e99b61aafac6e89799c24ec'
twilio_phone_number = '+18286565731'

# Function to fetch all mobile numbers from MySQL database
def fetch_mobile_numbers():
    try:
        # Connect to the database
        conn = mysql.connector.connect(**db_config)
        cursor = conn.cursor()

        # Query to fetch all mobile numbers
        query = "SELECT phone_number FROM airline_db.passengers"
        cursor.execute(query)

        # Fetch all results
        results = cursor.fetchall()

        return [row[0] for row in results] if results else []

    except mysql.connector.Error as err:
        print(f"Error: {err}")
        return []

    finally:
        if conn.is_connected():
            cursor.close()
            conn.close()

# Function to initiate a call using Twilio
def initiate_call(to_number):
    try:
        # Initialize Twilio client
        client = Client(twilio_account_sid, twilio_auth_token)

        # Make the call
        call = client.calls.create(
            url='http://demo.twilio.com/docs/voice.xml',  # TwiML URL for the call
            to=to_number,
            from_=twilio_phone_number
        )

        print(f"Call initiated to {to_number}. Call SID: {call.sid}")

    except Exception as e:
        print(f"Failed to initiate call to {to_number}: {e}")

# Main function
def main():
    # Fetch all mobile numbers from the database
    mobile_numbers = fetch_mobile_numbers()

    if mobile_numbers:
        # Initiate calls to all numbers
        for number in mobile_numbers:
            initiate_call(number)
    else:
        print("No mobile numbers to call.")

if __name__ == "__main__":
    main()
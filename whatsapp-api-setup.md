# WhatsApp API Setup

## Environment Variables

Add the following environment variables to your `.env` file:

```
# WhatsApp API Configuration
WHATSAPP_API_ENDPOINT=https://us-central1-pristine-nomad-264707.cloudfunctions.net/SendTemplateWhatsappV2
WHATSAPP_FROM_MOBILE_NO=918200599525
WHATSAPP_FB_TOKEN=EAB8uAArmndABO4ewJWGktpcbuBfZAiFV6XUIOYdEr9FR8L05gprGxlb1Sx9DNFwY9q1A8f3NcZAcs4b7DhE9nZBT5ZACPLxNIL3J0vTNHpvuam7zfrsHcwYilrmVybIowxY7xWapORJIUoPMcqWykvnFjgsnBqvZBURIzKEkUMZAfdPlQJdQ0b6W63W2sBgJ6i
WHATSAPP_EMPLOYEE_NAME="Suhag Patel"
WHATSAPP_HOSTEL_LOCATION="Nikol"
WHATSAPP_HOSTEL_NAME="LN Stay"
WHATSAPP_CONTACT_NUMBER=9876543210
```

## API Integration

The WhatsAppService has been updated to use the WhatsApp API instead of Twilio. The new API endpoint is:

```
https://us-central1-pristine-nomad-264707.cloudfunctions.net/SendTemplateWhatsappV2
```

The message structure is now using the following template:

```json
{
    "FromMobileNo": "918200599525",
    "FBToken": "EAB8uAArmndABO4ewJWGktpcbuBfZAiFV6XUIOYdEr9FR8L05gprGxlb1Sx9DNFwY9q1A8f3NcZAcs4b7DhE9nZBT5ZACPLxNIL3J0vTNHpvuam7zfrsHcwYilrmVybIowxY7xWapORJIUoPMcqWykvnFjgsnBqvZBURIzKEkUMZAfdPlQJdQ0b6W63W2sBgJ6i",
    "toMobileNo": "[RECIPIENT_PHONE_NUMBER]",
    "TemplateName": "inoutpunch",
    "TemplateLanguage": "en",
    "TemplateMsgString": "{{ 1 }}\nDear {{ 2 }},\n\nWe would like to inform you that your ward {{ 3 }} has {{ 4 }} the hostel premises {{ 5 }} at {{ 6 }}\n\nIf you have any questions, feel free to contact us at {{ 7 }}\n\n- {{ 8 }}\n\n{{ 9 }}",
    "EmployeeName": "Suhag Patel",
    "Variables": [
        { "Variable": "Greetings" },
        { "Variable": "[GUARDIAN_NAME]" },
        { "Variable": "[STUDENT_NAME]" },
        { "Variable": "[ENTERED/EXITED]" },
        { "Variable": "[HOSTEL_LOCATION]" },
        { "Variable": "[TIME]" },
        { "Variable": "[CONTACT_NUMBER]" },
        { "Variable": "[HOSTEL_NAME]" },
        { "Variable": "Thank you" }
    ]
}
```

The variables are dynamically filled based on the punch log data.

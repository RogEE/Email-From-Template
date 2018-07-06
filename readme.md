# Email From Template

This plugin emails the enclosed content to a provided email address.

## PARAMETERS:

from - sender email address (default: site webmaster)
to - destination email address (default: site webmaster)
cc - email addresses to carbon copy
bcc - email addresses to blind carbon copy
subject - email subject line (default: template URI)
mailtype - "text" or "html"
alt_message - a plain-text fallback for use with HTML emails
decode_subject_entities - Set to "no" if you don't want to parse the HTML entities in the subject line.
decode_message_entities - Set to "no" if you don't want to parse the HTML entities in the message text.
echo - Set to "off" if you don't want to display the tag contents in the template.

## VARIABLES:

{to}
{from}
{subject}
{ip}
{httpagent}
{uri_string}

EXAMPLE USAGE:

```
{exp:email_from_template to="admin@ee.com" from="server@ee.com" subject="Hello!" echo="off"}

	This tag content is being viewed at {uri_string}. Sending notification to {to}!

{/exp:email_from_template}	
```

## USING WITH OTHER PLUGINS AND TAGS:

When you want to email the output of other tags, put Email_from_Template INSIDE the other tag and use parse="inward" on the outer tags.
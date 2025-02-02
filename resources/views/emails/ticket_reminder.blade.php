<!DOCTYPE html>
<html>
  <head>
    <style>
      /* General email reset styles */
      body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
      }
      table {
        width: 100%;
        border-spacing: 0;
      }
      .email-container {
        max-width: 600px;
        margin: 0 auto;
        background-color: #ffffff;
        padding: 20px;
        float: left;
      }
      h1,
      p {
        margin: 0 0 10px;
      }
      .button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #00aaff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
      }
      .content-table {
        width: 100%;
        margin: 20px 0;
      }
      .content-table td {
        padding: 5px 0;
      }
      .content-table td:first-child {
        font-weight: bold;
      }
      .footer {
        margin-top: 20px;
        text-align: center;
        color: #888;
      }
    </style>
  </head>
  <body>
    <table role="presentation">
      <tr>
        <td>
          <div class="email-container">
            <p>Hi {{@$user_name}},</p>

            <p>
              This is a reminder regarding your Ticket {{@$ticketId}} in the
              lead form system.
            </p>

            <p><strong>Ticket details:</strong></p>

            <table class="content-table">
              <tr>
                <td>Customer</td>
                <td>: {{@$company_name}}</td>
              </tr>
              <tr>
                <td>Ticket assigned to</td>
                <td>: {{@$user_name}}</td>
              </tr>
              @if(@$note)
              <tr>
                <td>Notes</td>
                <td>: {{@$note}}</td>
              </tr>
              @endif
            </table>

            <p>For more details, please click the button below:</p>

            <p>
              <a href="{{route('ticket-list')}}" style="color: #ffffff !important" class="button"
                >View Ticket Details</a
              >
            </p>
          </div>
        </td>
      </tr>
    </table>
  </body>
</html>

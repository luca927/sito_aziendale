<?php
echo password_hash("admin123", PASSWORD_BCRYPT);
echo password_hash("password", PASSWORD_BCRYPT);
echo password_hash('Temp1234!', PASSWORD_DEFAULT);
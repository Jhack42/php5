-- Permisos de correo
GRANT EXECUTE ON UTL_SMTP TO haraka;
GRANT EXECUTE ON UTL_TCP TO haraka;
GRANT EXECUTE ON UTL_INADDR TO haraka;
GRANT SELECT ON DBA_NETWORK_ACLS TO haraka;
GRANT EXECUTE ON DBMS_ALERT TO haraka;
GRANT EXECUTE ON DBMS_NETWORK_ACL_ADMIN TO haraka;

-- Permisos de desarrollo
GRANT CREATE TABLE, CREATE VIEW TO haraka;
GRANT CREATE SEQUENCE, CREATE PROCEDURE TO haraka;
GRANT CREATE SYNONYM, CREATE TRIGGER TO haraka;
GRANT UNLIMITED TABLESPACE TO haraka;
GRANT CONNECT, RESOURCE TO haraka;

















-- -----------------------------------
GRANT CREATE TABLE TO haraka; 
GRANT CREATE VIEW TO haraka; 
GRANT CREATE SEQUENCE TO haraka; 
GRANT CREATE PROCEDURE TO haraka; 
GRANT CREATE SYNONYM TO haraka; 
GRANT UNLIMITED TABLESPACE TO haraka; 
GRANT CREATE SESSION TO haraka; 
GRANT CREATE TRIGGER TO haraka; 
GRANT CREATE SEQUENCE TO haraka; 
GRANT CREATE TABLE TO haraka; 
GRANT UNLIMITED TABLESPACE TO haraka; 
GRANT CONNECT, RESOURCE TO haraka; 
GRANT CREATE SESSION, CREATE TABLE, CREATE PROCEDURE, CREATE TRIGGER TO haraka; 


GRANT CREATE JOB TO haraka;
GRANT MANAGE SCHEDULER TO haraka;


-- Permisos de correo
GRANT EXECUTE ON UTL_SMTP TO haraka;
GRANT EXECUTE ON UTL_TCP TO haraka;
GRANT EXECUTE ON UTL_INADDR TO haraka;
GRANT SELECT ON DBA_NETWORK_ACLS TO haraka;
GRANT EXECUTE ON DBMS_ALERT TO haraka;
GRANT EXECUTE ON DBMS_NETWORK_ACL_ADMIN TO haraka;

-- Permisos de desarrollo
GRANT CREATE TABLE, CREATE VIEW TO haraka;
GRANT CREATE SEQUENCE, CREATE PROCEDURE TO haraka;
GRANT CREATE SYNONYM, CREATE TRIGGER TO haraka;
GRANT UNLIMITED TABLESPACE TO haraka;
GRANT CONNECT, RESOURCE TO haraka;



BEGIN
  DBMS_NETWORK_ACL_ADMIN.CREATE_ACL(
    acl => 'smtp_acl_haraka.xml',
    description => 'SMTP Access for haraka',
    principal => 'HARAKA',
    is_grant => TRUE,
    privilege => 'connect'
  );
  
  DBMS_NETWORK_ACL_ADMIN.ASSIGN_ACL(
    acl => 'smtp_acl_haraka.xml',
    host => 'localhost'
  );
  COMMIT;
END;
/





BEGIN
  haraka.enviar_correo_simple;
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE('Error detallado: ' || DBMS_UTILITY.FORMAT_ERROR_STACK);
END;
/






CREATE OR REPLACE PROCEDURE haraka.enviar_correo_simple 
IS
   v_mailhost    VARCHAR2(255) := 'localhost';
   v_port        NUMBER := 2525;
   v_from        VARCHAR2(255) := 'cacereshilasacajhack@gmail.com';
   v_to          VARCHAR2(255) := 'cacereshilasacajhack@gmail.com';
   v_subject     VARCHAR2(255) := 'Prueba de relay SMTP desde Oracle';
   v_message     VARCHAR2(4000) := 'Este correo fue enviado a través de Haraka como relay SMTP';
   v_mail_conn   UTL_SMTP.connection;
   v_crlf        VARCHAR2(2) := CHR(13) || CHR(10);
BEGIN
   v_mail_conn := UTL_SMTP.open_connection(v_mailhost, v_port);
   UTL_SMTP.helo(v_mail_conn, v_mailhost);
   UTL_SMTP.mail(v_mail_conn, v_from);
   UTL_SMTP.rcpt(v_mail_conn, v_to);
   UTL_SMTP.open_data(v_mail_conn);
   UTL_SMTP.write_data(v_mail_conn, 'From: ' || v_from || v_crlf);
   UTL_SMTP.write_data(v_mail_conn, 'To: ' || v_to || v_crlf);
   UTL_SMTP.write_data(v_mail_conn, 'Subject: ' || v_subject || v_crlf);
   UTL_SMTP.write_data(v_mail_conn, v_crlf);
   UTL_SMTP.write_data(v_mail_conn, v_message || v_crlf);
   UTL_SMTP.close_data(v_mail_conn);
   UTL_SMTP.quit(v_mail_conn);
   DBMS_OUTPUT.PUT_LINE('Correo enviado exitosamente.');
EXCEPTION
   WHEN OTHERS THEN
       DBMS_OUTPUT.PUT_LINE('Error al enviar el correo: ' || SQLERRM);
END;
/
CREATE USER omesa IDENTIFIED BY omesa;


GRANT CREATE TABLE TO omesa; 
GRANT CREATE VIEW TO omesa; 
GRANT CREATE SEQUENCE TO omesa; 
GRANT CREATE PROCEDURE TO omesa; 
GRANT CREATE SYNONYM TO omesa; 
GRANT UNLIMITED TABLESPACE TO omesa; 
GRANT CREATE SESSION TO omesa; 
GRANT CREATE TRIGGER TO omesa; 
GRANT CREATE SEQUENCE TO omesa; 
GRANT CREATE TABLE TO omesa; 
GRANT UNLIMITED TABLESPACE TO omesa; 
GRANT CONNECT, RESOURCE TO omesa; 
GRANT CREATE SESSION, CREATE TABLE, CREATE PROCEDURE, CREATE TRIGGER TO omesa; 
GRANT EXECUTE ON UTL_MAIL TO omesa; 
GRANT EXECUTE ON UTL_SMTP TO omesa; 
GRANT SELECT ON DBA_NETWORK_ACLS TO omesa; 
GRANT EXECUTE ON UTL_MAIL TO omesa; 
GRANT EXECUTE ON DBMS_ALERT TO omesa;



GRANT CONNECT, RESOURCE TO omesa;
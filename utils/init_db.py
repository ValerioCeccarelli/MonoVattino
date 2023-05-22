import psycopg2

file_name = input('Enter file name: ') or "db.sql"
host = input('Enter host: ') or "localhost"
port = input('Enter port: ') or "5432"
database = input('Enter database: ') or "postgres"
user = input('Enter user: ') or "postgres"
password = input('Enter password: ') or "postgres"

with open(file_name, 'r') as f:
    sql_query = f.read()

    conn = psycopg2.connect(database=database,
                        host=host,
                        user=user,
                        password=password,
                        port=port)

    cur = conn.cursor()

    cur.execute(sql_query)

    print("Database created successfully")
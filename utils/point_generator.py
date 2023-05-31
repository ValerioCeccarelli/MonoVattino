import random

# Rome coordinates bounds (42.012735, 12.285497) (41.778089, 12.654691)

print("Tool to generate random points:")

lat_start = float(input("Insert latitude start: ") or "41.778089")
lon_start = float(input("Insert longitude start: ") or "12.654691")

lat_end = float(input("Insert latitude end: ") or "42.012735")
lon_end = float(input("Insert longitude end: ") or "12.285497")

n_companies = int(input("Insert number of companies: ") or "6")
companies = [i for i in range(1, n_companies + 1)]

n_points = int(input("Insert number of points: ") or "1000")

scooters = []

for i in range(n_points):
    lat = random.uniform(lat_start, lat_end)
    lon = random.uniform(lon_start, lon_end)
    comp = random.choice(companies)
    bat = random.randint(1, 100)
    scooters.append((lat, lon, bat, comp))

sql_template = 'INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES ({lat}, {lon}, {bat}, {comp});'

file_name = input("Insert file name: ") or "points.sql"

with open(file_name, 'w') as f:
    for scooter in scooters:
        sql = sql_template.format(lat=scooter[0], lon=scooter[1], bat=scooter[2], comp=scooter[3])
        f.write(sql + '\n')

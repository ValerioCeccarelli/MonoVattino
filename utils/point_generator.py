import random

# Rome coordinates bounds (42.012735, 12.285497) (41.778089, 12.654691)

print("Tool to generate random points:")

lat_start = float(input("Insert latitude start: "))
lon_start = float(input("Insert longitude start: "))

lat_end = float(input("Insert latitude end: "))
lon_end = float(input("Insert longitude end: "))

n_companies = int(input("Insert number of companies: "))
companies = [i for i in range(1, n_companies + 1)]

n_points = int(input("Insert number of points: "))

points = []

for i in range(n_points):
    lat = random.uniform(lat_start, lat_end)
    lon = random.uniform(lon_start, lon_end)
    comp = random.choice(companies)
    points.append((lat, lon, comp))

sql_template = 'INSERT INTO scooters (latitude, longitude, battery_level, company) VALUES ({lat}, {lon}, 100, {comp});'

file_name = input("Insert file name: ")

with open('file_name', 'w') as f:
    for point in points:
        sql = sql_template.format(lat=point[0], lon=point[1], comp=point[2])
        f.write(sql + '\n')

import datetime
import os
from azure.storage import BlobService

blobService = BlobService("imovm", "uFZvYdkl+0B5mCP39XzDMShuwUQ7oOsYlAks6otoKNnqImk/tI05yI4W/mfbFGXh/GgjZ5XIUJgF6qUO59Ad3A==")
container = "dbbackups"


def backupFile(filePath):
    with open(filePath) as file:
        blobService.put_block_blob_from_file(container, filePath, file)
      
def dumpDatabase(dbName):
    now = datetime.datetime.now()
    filename = str(now).replace(" ", "-") + "-" + dbName + ".sql"
    os.system("mysqldump -u ilmarch_libraria -pilmarching " + dbName + " > " + filename)
    return filename
    
def backupDatabase(dbName):
    backup = dumpDatabase(dbName)
    backupFile(backup)
    os.remove(backup)
    
backupDatabase("ilmarch_library")
backupDatabase("ilmarch_wordpress")
backupDatabase("ilmarch_IPS")

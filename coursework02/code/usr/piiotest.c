/*
 ============================================================================
 Name        : piiotest.c
 Author      : AR
 Modified By : Cameron McCallion
 Version     : 0.1
 Copyright   : See Abertay copyright notice
 Description : Test application for piio driver
 ============================================================================
 */

#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/ioctl.h>

#include "piio.h"

/*
 * Functions for the ioctl calls
 *
 root@raspberrypi:/home/pi# ./piiotest writepin 3 1
 User App - CGI
 WRITE:Requested pin:3 - val:1 - desc:desc
 Exit 0
 root@raspberrypi:/home/pi# ./piiotest read 3
 User App - CGI
 READ:Requested  pin:3 - val:1 - desc:LKMpin
 Exit 0
 */

gpio_pin apin;
lkm_data lkmdata;

void write_to_driver(int fd) {
	int ret;
	/* Write to kernel space - see dmesg command*/
	strcpy(lkmdata.data, "This is from user application");
	lkmdata.len = 32;
	lkmdata.type = 'w';
	ret = ioctl(fd, IOCTL_PIIO_WRITE, &lkmdata);

	if (ret < 0) {
		printf("Function failed:%d\n", ret);
		exit(-1);
	}

}

void read_from_drvier(int fd) {
	int ret;

	/*Read from kernel space - see dmesg command*/
	strcpy(lkmdata.data, "");
	ret = ioctl(fd, IOCTL_PIIO_READ, &lkmdata);

	if (ret < 0) {
		printf("Function failed:%d\n", ret);
		exit(-1);
	}

	printf("Message from driver: %s\n", lkmdata.data);
}

int main(int argc, char *argv[]) {
	printf("User App\n");
	int fd, ret;
	char *msg = "Message passed by ioctl\n";

	fd = open("//dev//dCameronMcCallion", O_RDWR);
	if (fd < 0) {
		printf("Can't open device file: %s\n", DEVICE_NAME);
		exit(-1);
	}


	if (argc > 1) {
		if (!strncmp(argv[1], "readmsg", 8)) {
			read_from_drvier(fd);

		}

		if (!strncmp(argv[1], "writemsg", 9)) {
			write_to_driver(fd);
		}

		if (!strncmp(argv[1], "read", 8)) {
			/*  Pass GPIO struct with IO control */
			memset(&apin , 0, sizeof(apin));
			strcpy(apin.desc, "LKMpin");
			apin.pin =  strtol (argv[2],NULL,10);
			/* Pass 'apin' struct to 'fd' with IO control*/
		        ret = ioctl(fd, IOCTL_PIIO_GPIO_READ, &apin);

		        if (ret < 0) {
				printf("Function failed:%d\n", ret);
				exit(-1);
		        }

			printf("READ:Requested  pin:%i - val:%i - desc:%s\n" , apin.pin , apin.value, apin.desc);
		}

		if (!strncmp(argv[1], "write", 9)) {
			/*  Pass GPIO struct with IO control */
			memset(&apin , 0, sizeof(apin));
			strcpy(apin.desc, "WriteOpt");
			apin.pin = strtol(argv[2],NULL,10);
			apin.value = strtol(argv[3],NULL,10);
			/* Pass 'apin' struct to 'fd' with IO control*/
			printf("WRITE:Requested pin:%i - val:%i - desc:%s\n" , apin.pin , apin.value, apin.desc);
			ret = ioctl(fd, IOCTL_PIIO_GPIO_WRITE, &apin);
		}

		if(!strncmp(argv[1], "toggle", 10)) {
			memset(&apin, 0, sizeof(apin));
			strcpy(apin.desc, "tgl");
			apin.pin = strtol(argv[2], NULL, 10);
			apin.value = strtol(argv[3], NULL, 10);
			int count = strtol(argv[4], NULL, 10);
			int interval = strtol(argv[5], NULL, 10);
			int i;
			for(i = 0; i < count; i++) {
					printf("TOGGLE:Requesting pin:%i - val:%i - desc:%s\n", apin.pin, apin.value, apin.desc);
					ret = ioctl(fd, IOCTL_PIIO_GPIO_WRITE, &apin);
					usleep(interval);
					apin.value = apin.value == 1 ? 0 : 1;
			}
		}



	}

	printf("Exit 0\n");

	close(fd);
	return 0;
}